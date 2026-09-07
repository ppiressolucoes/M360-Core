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

    [string] $ClientExecutable,

    [switch] $RequireTls
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Resolve-ClientExecutable {
    param([string] $ExplicitPath)

    if ($ExplicitPath) {
        if (-not (Test-Path -LiteralPath $ExplicitPath -PathType Leaf)) {
            throw "Cliente não encontrado: $ExplicitPath"
        }
        return (Resolve-Path -LiteralPath $ExplicitPath).Path
    }

    foreach ($candidate in @('mariadb.exe', 'mysql.exe', 'mariadb', 'mysql')) {
        $command = Get-Command $candidate -ErrorAction SilentlyContinue
        if ($command) {
            return $command.Source
        }
    }

    $localToolsRoot = Join-Path $PSScriptRoot '..\..\local-tools'
    if (Test-Path -LiteralPath $localToolsRoot) {
        $portableClient = Get-ChildItem -LiteralPath $localToolsRoot -Filter 'mariadb.exe' -File -Recurse |
            Select-Object -First 1 -ExpandProperty FullName
        if ($portableClient) {
            return $portableClient
        }
    }

    throw 'mariadb ou mysql não foi encontrado. Informe -ClientExecutable ou prepare o cliente portátil.'
}

$client = Resolve-ClientExecutable -ExplicitPath $ClientExecutable
$helpText = (& $client --help 2>&1 | Out-String)
$tlsArguments = @()

if ($RequireTls) {
    if ($helpText -match '(?m)--ssl-verify-server-cert(?:[=\s,])') {
        $tlsArguments += '--ssl-verify-server-cert'
    } elseif ($helpText -match '(?m)--ssl-mode(?:[=\s])') {
        $tlsArguments += '--ssl-mode=REQUIRED'
    } elseif ($helpText -match '(?m)^\s*--ssl(?:[=\s,])') {
        $tlsArguments += '--ssl'
    } else {
        throw 'O cliente não expõe uma opção TLS reconhecida. Atualize-o ou use um túnel SSH autorizado.'
    }
}

$query = @'
SELECT 'connection' AS check_name, 1 AS ok;
SELECT VERSION() AS server_version,
       DATABASE() AS current_schema,
       CURRENT_USER() AS authenticated_as,
       @@session.time_zone AS session_time_zone,
       @@global.time_zone AS global_time_zone,
       @@system_time_zone AS system_time_zone;
SELECT TABLE_NAME, TABLE_TYPE
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN (
      'dim_times',
      'dim_competicoes',
      'fato_classificacao',
      'fato_jogos',
      'dim_competicao_fase_jogo'
  )
ORDER BY TABLE_NAME;
SHOW STATUS LIKE 'Ssl_cipher';
SELECT TABLE_NAME,
       ENGINE,
       TABLE_ROWS AS estimated_rows,
       ROUND((COALESCE(DATA_LENGTH, 0) + COALESCE(INDEX_LENGTH, 0)) / 1048576, 2) AS approximate_mb
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN (
      'dim_times',
      'dim_competicoes',
      'fato_classificacao',
      'fato_jogos',
      'dim_competicao_fase_jogo'
  )
ORDER BY TABLE_NAME;
SELECT privilege_scope, PRIVILEGE_TYPE, IS_GRANTABLE
FROM (
    SELECT 'GLOBAL' AS privilege_scope, PRIVILEGE_TYPE, IS_GRANTABLE
    FROM information_schema.USER_PRIVILEGES
    WHERE GRANTEE = CONCAT(
        QUOTE(SUBSTRING_INDEX(CURRENT_USER(), '@', 1)),
        '@',
        QUOTE(SUBSTRING_INDEX(CURRENT_USER(), '@', -1))
    )
    UNION ALL
    SELECT CONCAT('SCHEMA:', TABLE_SCHEMA), PRIVILEGE_TYPE, IS_GRANTABLE
    FROM information_schema.SCHEMA_PRIVILEGES
    WHERE TABLE_SCHEMA = DATABASE()
      AND GRANTEE = CONCAT(
          QUOTE(SUBSTRING_INDEX(CURRENT_USER(), '@', 1)),
          '@',
          QUOTE(SUBSTRING_INDEX(CURRENT_USER(), '@', -1))
      )
    UNION ALL
    SELECT CONCAT('TABLE:', TABLE_NAME), PRIVILEGE_TYPE, IS_GRANTABLE
    FROM information_schema.TABLE_PRIVILEGES
    WHERE TABLE_SCHEMA = DATABASE()
      AND GRANTEE = CONCAT(
          QUOTE(SUBSTRING_INDEX(CURRENT_USER(), '@', 1)),
          '@',
          QUOTE(SUBSTRING_INDEX(CURRENT_USER(), '@', -1))
      )
) AS effective_privileges
ORDER BY privilege_scope, PRIVILEGE_TYPE;
'@

$temporaryRoot = Join-Path ([System.IO.Path]::GetTempPath()) ("m360-dw-connect-" + [Guid]::NewGuid().ToString('N'))
New-Item -ItemType Directory -Path $temporaryRoot -Force | Out-Null
$queryPath = Join-Path $temporaryRoot 'connection-check.sql'
$outputPath = Join-Path $temporaryRoot 'connection-check.out'
$errorPath = Join-Path $temporaryRoot 'connection-check.err'
$query | Set-Content -LiteralPath $queryPath -Encoding UTF8

$securePassword = Read-Host "Senha do usuário somente leitura '$UserName'" -AsSecureString
$passwordPointer = [IntPtr]::Zero
$plainPassword = $null
$previousPassword = $env:MYSQL_PWD

try {
    $passwordPointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($securePassword)
    $plainPassword = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($passwordPointer)
    $env:MYSQL_PWD = $plainPassword

    $arguments = @(
        '--protocol=tcp',
        "--host=$HostName",
        "--port=$Port",
        "--user=$UserName",
        "--database=$Database",
        '--default-character-set=utf8mb4',
        '--batch',
        '--raw'
    ) + $tlsArguments

    $process = Start-Process `
        -FilePath $client `
        -ArgumentList $arguments `
        -NoNewWindow `
        -Wait `
        -PassThru `
        -RedirectStandardInput $queryPath `
        -RedirectStandardOutput $outputPath `
        -RedirectStandardError $errorPath

    if ($process.ExitCode -ne 0) {
        $detail = (Get-Content -LiteralPath $errorPath -Raw -ErrorAction SilentlyContinue).Trim()
        throw "Conexão reprovada pelo cliente, código $($process.ExitCode). $detail"
    }

    Write-Host 'Conexão somente leitura estabelecida.' -ForegroundColor Green
    Get-Content -LiteralPath $outputPath
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

    if (Test-Path -LiteralPath $temporaryRoot) {
        Remove-Item -LiteralPath $temporaryRoot -Recurse -Force
    }
}
