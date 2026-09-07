[CmdletBinding(DefaultParameterSetName = 'Text')]
param(
    [Parameter(Mandatory = $true, ParameterSetName = 'Text')]
    [ValidateNotNullOrEmpty()]
    [string] $Sql,

    [Parameter(Mandatory = $true, ParameterSetName = 'File')]
    [ValidateScript({ Test-Path -LiteralPath $_ -PathType Leaf })]
    [string] $SqlFile,

    [ValidateRange(1024, 65535)]
    [int] $Port = 3307
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$runtimeRoot = Join-Path $PSScriptRoot '..\..\local-data\dw-esportivo\runtime\mariadb-12.3.3'
$secretPath = Join-Path $runtimeRoot '.reader-password'
$localToolsRoot = Join-Path $PSScriptRoot '..\..\local-tools'
$client = Get-ChildItem -LiteralPath $localToolsRoot -Filter 'mariadb.exe' -File -Recurse |
    Select-Object -First 1 -ExpandProperty FullName

if (-not $client -or -not (Test-Path -LiteralPath $secretPath)) {
    throw 'Cliente ou credencial local somente leitura não encontrado.'
}

$arguments = @(
    '--protocol=tcp',
    '--host=127.0.0.1',
    "--port=$Port",
    '--user=m360_reader',
    '--database=m360_dw_local',
    '--disable-ssl',
    '--default-character-set=utf8mb4',
    '--batch',
    '--raw'
)

$readerPassword = (Get-Content -LiteralPath $secretPath -Raw).Trim()
$previousPassword = $env:MYSQL_PWD
try {
    $env:MYSQL_PWD = $readerPassword
    if ($PSCmdlet.ParameterSetName -eq 'Text') {
        & $client @arguments "--execute=$Sql"
        if ($LASTEXITCODE -ne 0) {
            throw "Consulta local falhou: código $LASTEXITCODE"
        }
    } else {
        $outputPath = Join-Path $runtimeRoot 'query.out'
        $errorPath = Join-Path $runtimeRoot 'query.err'
        $process = Start-Process `
            -FilePath $client `
            -ArgumentList $arguments `
            -NoNewWindow `
            -Wait `
            -PassThru `
            -RedirectStandardInput (Resolve-Path -LiteralPath $SqlFile).Path `
            -RedirectStandardOutput $outputPath `
            -RedirectStandardError $errorPath
        if ($process.ExitCode -ne 0) {
            $detail = (Get-Content -LiteralPath $errorPath -Raw -ErrorAction SilentlyContinue).Trim()
            throw "Consulta local falhou: $detail"
        }
        Get-Content -LiteralPath $outputPath
        Remove-Item -LiteralPath $outputPath,$errorPath -Force -ErrorAction SilentlyContinue
    }
} finally {
    if ($null -eq $previousPassword) {
        Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
    } else {
        $env:MYSQL_PWD = $previousPassword
    }
    $readerPassword = $null
}
