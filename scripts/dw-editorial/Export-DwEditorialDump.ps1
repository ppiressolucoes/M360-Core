[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [ValidatePattern('^[A-Za-z0-9._:-]+$')]
    [string] $HostName,

    [ValidateRange(1, 65535)]
    [int] $Port = 3306,

    [Parameter(Mandatory = $true)]
    [ValidatePattern('^[A-Za-z0-9_$-]+$')]
    [string] $Database,

    [Parameter(Mandatory = $true)]
    [ValidatePattern('^[A-Za-z0-9_.@-]+$')]
    [string] $UserName,

    [string] $DumpExecutable,

    [string] $OutputRoot = (Join-Path $PSScriptRoot '..\..\local-data\dw-esportivo\incoming'),

    [ValidatePattern('^[A-Za-z0-9._-]+$')]
    [string] $SourceLabel = 'dw-esportivo-production',

    [ValidateNotNullOrEmpty()]
    [string[]] $Tables = @(
        'dim_times',
        'dim_competicoes',
        'fato_classificacao',
        'fato_jogos',
        'dim_competicao_fase_jogo'
    ),

    [switch] $SchemaOnly,

    [switch] $RequireTls
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Resolve-DumpExecutable {
    param([string] $ExplicitPath)

    if ($ExplicitPath) {
        if (-not (Test-Path -LiteralPath $ExplicitPath -PathType Leaf)) {
            throw "Executável de dump não encontrado: $ExplicitPath"
        }

        return (Resolve-Path -LiteralPath $ExplicitPath).Path
    }

    foreach ($candidate in @('mariadb-dump.exe', 'mysqldump.exe', 'mariadb-dump', 'mysqldump')) {
        $command = Get-Command $candidate -ErrorAction SilentlyContinue
        if ($command) {
            return $command.Source
        }
    }

    $localToolsRoot = Join-Path $PSScriptRoot '..\..\local-tools'
    if (Test-Path -LiteralPath $localToolsRoot) {
        $portableClient = Get-ChildItem -LiteralPath $localToolsRoot -Filter 'mariadb-dump.exe' -File -Recurse |
            Select-Object -First 1 -ExpandProperty FullName
        if ($portableClient) {
            return $portableClient
        }
    }

    throw 'mariadb-dump ou mysqldump não foi encontrado. Instale somente o cliente MariaDB/MySQL ou informe -DumpExecutable.'
}

function Invoke-Dump {
    param(
        [string] $Executable,
        [string[]] $Arguments,
        [string] $OutputFile,
        [string] $ErrorFile
    )

    $process = Start-Process `
        -FilePath $Executable `
        -ArgumentList $Arguments `
        -NoNewWindow `
        -Wait `
        -PassThru `
        -RedirectStandardOutput $OutputFile `
        -RedirectStandardError $ErrorFile

    if ($process.ExitCode -ne 0) {
        $detail = if (Test-Path -LiteralPath $ErrorFile) {
            (Get-Content -LiteralPath $ErrorFile -Raw -ErrorAction SilentlyContinue).Trim()
        } else {
            ''
        }

        throw "O cliente de dump encerrou com código $($process.ExitCode). $detail"
    }

    if (-not (Test-Path -LiteralPath $OutputFile) -or (Get-Item -LiteralPath $OutputFile).Length -eq 0) {
        throw "O cliente não produziu conteúdo em $OutputFile"
    }

    Remove-Item -LiteralPath $ErrorFile -Force -ErrorAction SilentlyContinue
}

function Remove-DefinerIdentity {
    param([string] $SqlPath)

    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    $sql = [System.IO.File]::ReadAllText($SqlPath)
    $definerPattern = '(?i)DEFINER\s*=\s*(?:`[^`]*`|[^@\s]+)@(?:`[^`]*`|[^\s*]+)'
    $sanitized = [System.Text.RegularExpressions.Regex]::Replace($sql, $definerPattern, 'DEFINER=CURRENT_USER')
    [System.IO.File]::WriteAllText($SqlPath, $sanitized, $utf8NoBom)
}

foreach ($table in $Tables) {
    if ($table -notmatch '^[A-Za-z0-9_$-]+$') {
        throw "Nome de tabela inválido: $table"
    }
}

$dumpTool = Resolve-DumpExecutable -ExplicitPath $DumpExecutable
$versionText = (& $dumpTool --version 2>&1 | Out-String).Trim()
$helpText = (& $dumpTool --help 2>&1 | Out-String)

$tlsArguments = @()
if ($RequireTls) {
    if ($helpText -match '(?m)--ssl-verify-server-cert(?:[=\s,])') {
        $tlsArguments += '--ssl-verify-server-cert'
    } elseif ($helpText -match '(?m)--ssl-mode(?:[=\s])') {
        $tlsArguments += '--ssl-mode=REQUIRED'
    } elseif ($helpText -match '(?m)^\s*--ssl(?:[=\s,])') {
        $tlsArguments += '--ssl'
    } else {
        throw 'O cliente informado não expõe uma opção TLS reconhecida. Atualize o cliente ou use um túnel SSH autorizado.'
    }
}

$compatibilityArguments = @()
if ($helpText -match '(?m)--column-statistics(?:[=\s])') {
    $compatibilityArguments += '--column-statistics=0'
}
if ($helpText -match '(?m)--no-tablespaces(?:[=\s,])') {
    $compatibilityArguments += '--no-tablespaces'
}

$commonArguments = @(
    '--protocol=tcp',
    "--host=$HostName",
    "--port=$Port",
    "--user=$UserName",
    '--default-character-set=utf8mb4',
    '--single-transaction',
    '--quick',
    '--skip-lock-tables',
    '--hex-blob'
) + $tlsArguments + $compatibilityArguments

$resolvedOutputRoot = [System.IO.Path]::GetFullPath($OutputRoot)
$timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$runDirectory = Join-Path $resolvedOutputRoot "$timestamp-$SourceLabel"
New-Item -ItemType Directory -Path $runDirectory -Force | Out-Null

$schemaPath = Join-Path $runDirectory 'dw-esportivo-schema.sql'
$dataPath = Join-Path $runDirectory 'dw-esportivo-core-data.sql'
$schemaErrorPath = Join-Path $runDirectory 'schema.stderr.log'
$dataErrorPath = Join-Path $runDirectory 'data.stderr.log'

$securePassword = Read-Host "Senha do usuário somente leitura '$UserName'" -AsSecureString
$passwordPointer = [IntPtr]::Zero
$plainPassword = $null
$previousPassword = $env:MYSQL_PWD

try {
    $passwordPointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($securePassword)
    $plainPassword = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($passwordPointer)
    $env:MYSQL_PWD = $plainPassword

    $schemaArguments = $commonArguments + @(
        '--no-data',
        '--skip-comments',
        '--skip-dump-date',
        '--skip-triggers',
        $Database
    )
    Invoke-Dump -Executable $dumpTool -Arguments $schemaArguments -OutputFile $schemaPath -ErrorFile $schemaErrorPath

    if (-not $SchemaOnly) {
        $dataArguments = $commonArguments + @(
            '--no-create-info',
            '--skip-comments',
            '--skip-dump-date',
            '--skip-triggers',
            '--complete-insert',
            $Database
        ) + $Tables
        Invoke-Dump -Executable $dumpTool -Arguments $dataArguments -OutputFile $dataPath -ErrorFile $dataErrorPath
    }
} finally {
    if ($null -eq $previousPassword) {
        Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
    } else {
        $env:MYSQL_PWD = $previousPassword
    }

    $plainPassword = $null
    if ($passwordPointer -ne [IntPtr]::Zero) {
        [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($passwordPointer)
    }
}

Remove-DefinerIdentity -SqlPath $schemaPath

$exportedFiles = @($schemaPath)
if (-not $SchemaOnly) {
    $exportedFiles += $dataPath
}

$manifestFiles = foreach ($filePath in $exportedFiles) {
    $item = Get-Item -LiteralPath $filePath
    $hash = Get-FileHash -LiteralPath $filePath -Algorithm SHA256
    [ordered]@{
        name   = $item.Name
        bytes  = $item.Length
        sha256 = $hash.Hash.ToLowerInvariant()
    }
}

$manifest = [ordered]@{
    formatVersion = 1
    sourceLabel = $SourceLabel
    generatedAtUtc = (Get-Date).ToUniversalTime().ToString('o')
    dumpClient = $versionText
    schemaOnly = [bool] $SchemaOnly
    exportedTables = if ($SchemaOnly) { @() } else { $Tables }
    security = [ordered]@{
        passwordOnCommandLine = $false
        definerIdentityRemoved = $true
        tlsRequiredByScript = [bool] $RequireTls
    }
    files = @($manifestFiles)
}

$manifestPath = Join-Path $runDirectory 'manifest.json'
$manifest | ConvertTo-Json -Depth 6 | Set-Content -LiteralPath $manifestPath -Encoding UTF8

$archivePath = "$runDirectory.zip"
Compress-Archive -Path (Join-Path $runDirectory '*') -DestinationPath $archivePath -CompressionLevel Optimal -Force
$archiveHash = (Get-FileHash -LiteralPath $archivePath -Algorithm SHA256).Hash.ToLowerInvariant()
$archiveHashPath = "$archivePath.sha256"
"$archiveHash  $([System.IO.Path]::GetFileName($archivePath))" | Set-Content -LiteralPath $archiveHashPath -Encoding ASCII

Write-Host ''
Write-Host 'Dump concluído.' -ForegroundColor Green
Write-Host "Pasta:   $runDirectory"
Write-Host "Pacote:  $archivePath"
Write-Host "SHA-256: $archiveHash"
