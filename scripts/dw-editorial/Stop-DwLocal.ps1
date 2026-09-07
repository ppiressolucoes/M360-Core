[CmdletBinding()]
param(
    [ValidateRange(1024, 65535)]
    [int] $Port = 3307
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Test-LocalTcpPort {
    param([int] $TcpPort)

    $client = [System.Net.Sockets.TcpClient]::new()
    try {
        $task = $client.ConnectAsync('127.0.0.1', $TcpPort)
        return $task.Wait(750) -and $client.Connected
    } catch {
        return $false
    } finally {
        $client.Dispose()
    }
}

$runtimeRoot = Join-Path $PSScriptRoot '..\..\local-data\dw-esportivo\runtime\mariadb-12.3.3'
$secretPath = Join-Path $runtimeRoot '.root-password'
$localToolsRoot = Join-Path $PSScriptRoot '..\..\local-tools'
$admin = Get-ChildItem -LiteralPath $localToolsRoot -Filter 'mariadb-admin.exe' -File -Recurse |
    Select-Object -First 1 -ExpandProperty FullName

$isReachable = Test-LocalTcpPort -TcpPort $Port
if (-not $isReachable) {
    Write-Host 'DW local já está parado.'
    exit 0
}

if (-not $admin -or -not (Test-Path -LiteralPath $secretPath)) {
    throw 'Cliente administrativo ou segredo local não encontrado.'
}

$rootPassword = (Get-Content -LiteralPath $secretPath -Raw).Trim()
$previousPassword = $env:MYSQL_PWD
try {
    $env:MYSQL_PWD = $rootPassword
    & $admin --protocol=tcp --host=127.0.0.1 "--port=$Port" --user=root --disable-ssl shutdown
    if ($LASTEXITCODE -ne 0) {
        throw "Falha ao encerrar o MariaDB local: código $LASTEXITCODE"
    }
} finally {
    if ($null -eq $previousPassword) {
        Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
    } else {
        $env:MYSQL_PWD = $previousPassword
    }
    $rootPassword = $null
}

Write-Host 'DW local encerrado.' -ForegroundColor Green
