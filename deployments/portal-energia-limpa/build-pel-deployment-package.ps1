param(
    [string] $Version = '0.1.3'
)

$ErrorActionPreference = 'Stop'
$source = Join-Path $PSScriptRoot 'plugin'
$output = Join-Path $PSScriptRoot ("outputs\\m360-pel-controlled-deployment-v{0}" -f $Version)
$zipPath = Join-Path $output ("m360-pel-controlled-deployment-v{0}.zip" -f $Version)

if (-not (Test-Path -LiteralPath (Join-Path $source 'm360-pel-deployment.php'))) {
    throw 'Arquivo principal do complemento não encontrado.'
}

New-Item -ItemType Directory -Force -Path $output | Out-Null
if (Test-Path -LiteralPath $zipPath) { Remove-Item -LiteralPath $zipPath -Force }

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem
$stream = [IO.File]::Open($zipPath, [IO.FileMode]::CreateNew)
try {
    $archive = [IO.Compression.ZipArchive]::new($stream, [IO.Compression.ZipArchiveMode]::Create, $false)
    try {
        [void] $archive.CreateEntry('m360-pel-controlled-deployment/')
        Get-ChildItem -LiteralPath $source -File -Recurse | Sort-Object FullName | ForEach-Object {
            $relative = $_.FullName.Substring($source.Length).TrimStart([char[]]"\\/")
            $entry = $archive.CreateEntry(('m360-pel-controlled-deployment/' + $relative.Replace('\', '/')), [IO.Compression.CompressionLevel]::Optimal)
            $input = [IO.File]::OpenRead($_.FullName)
            $target = $entry.Open()
            try { $input.CopyTo($target) } finally { $target.Dispose(); $input.Dispose() }
        }
    } finally { $archive.Dispose() }
} finally { $stream.Dispose() }

$check = [IO.Compression.ZipFile]::OpenRead($zipPath)
try {
    if ($null -eq $check.GetEntry('m360-pel-controlled-deployment/m360-pel-deployment.php')) {
        throw 'O ZIP não contém o arquivo principal esperado.'
    }
} finally { $check.Dispose() }

$hash = Get-FileHash -Algorithm SHA256 -LiteralPath $zipPath
[PSCustomObject]@{ Version = $Version; Package = $zipPath; SHA256 = $hash.Hash }
