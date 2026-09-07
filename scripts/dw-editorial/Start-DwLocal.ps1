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
$configPath = Join-Path $runtimeRoot 'data\my.ini'
$localToolsRoot = Join-Path $PSScriptRoot '..\..\local-tools'
$server = Get-ChildItem -LiteralPath $localToolsRoot -Filter 'mariadbd.exe' -File -Recurse |
    Select-Object -First 1 -ExpandProperty FullName

if (-not $server -or -not (Test-Path -LiteralPath $configPath)) {
    throw 'Runtime local não inicializado. Consulte scripts/dw-editorial/README.md.'
}

$isReachable = Test-LocalTcpPort -TcpPort $Port
if ($isReachable) {
    Write-Host "DW local já está ativo em 127.0.0.1:$Port." -ForegroundColor Green
    exit 0
}

$stdoutPath = Join-Path $runtimeRoot 'mariadbd.stdout.log'
$stderrPath = Join-Path $runtimeRoot 'mariadbd.stderr.log'
$configArgument = "--defaults-file=`"$([System.IO.Path]::GetFullPath($configPath))`""
$process = Start-Process `
    -FilePath $server `
    -ArgumentList @($configArgument, "--port=$Port", '--console') `
    -WorkingDirectory (Split-Path -Parent $server) `
    -WindowStyle Hidden `
    -PassThru `
    -RedirectStandardOutput $stdoutPath `
    -RedirectStandardError $stderrPath

$process.Id | Set-Content -LiteralPath (Join-Path $runtimeRoot 'mariadbd.pid') -Encoding ASCII

$deadline = (Get-Date).AddSeconds(20)
do {
    Start-Sleep -Milliseconds 500
    if ($process.HasExited) {
        $detail = (Get-Content -LiteralPath $stderrPath -Raw -ErrorAction SilentlyContinue).Trim()
        throw "MariaDB local encerrou durante a inicialização. $detail"
    }
    $isReachable = Test-LocalTcpPort -TcpPort $Port
} while (-not $isReachable -and (Get-Date) -lt $deadline)

if (-not $isReachable) {
    throw "MariaDB local não abriu a porta $Port dentro do prazo."
}

Write-Host "DW local iniciado em 127.0.0.1:$Port." -ForegroundColor Green
