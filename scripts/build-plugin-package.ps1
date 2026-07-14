param(
    [Parameter(Mandatory = $true)]
    [string] $Version
)

$ErrorActionPreference = 'Stop'
$repoRoot = Split-Path -Parent $PSScriptRoot
$source = Join-Path $repoRoot 'plugin'
$output = Join-Path $repoRoot ("outputs\m360-core-v{0}" -f $Version)
$zipPath = Join-Path $output ("m360-core-v{0}.zip" -f $Version)

if (-not (Test-Path -LiteralPath (Join-Path $source 'm360-core.php'))) {
    throw 'Arquivo principal plugin/m360-core.php não encontrado.'
}

New-Item -ItemType Directory -Force -Path $output | Out-Null
if (Test-Path -LiteralPath $zipPath) {
    Remove-Item -LiteralPath $zipPath -Force
}

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$stream = [IO.File]::Open($zipPath, [IO.FileMode]::CreateNew)
try {
    $archive = [IO.Compression.ZipArchive]::new($stream, [IO.Compression.ZipArchiveMode]::Create, $false)
    try {
        [void] $archive.CreateEntry('m360-core/')
        Get-ChildItem -LiteralPath $source -File -Recurse | Sort-Object FullName | ForEach-Object {
            $relative = $_.FullName.Substring($source.Length).TrimStart([char[]]"\/")
            $entryName = 'm360-core/' + $relative.Replace('\', '/')
            $entry = $archive.CreateEntry($entryName, [IO.Compression.CompressionLevel]::Optimal)
            $input = [IO.File]::OpenRead($_.FullName)
            $target = $entry.Open()
            try {
                $input.CopyTo($target)
            } finally {
                $target.Dispose()
                $input.Dispose()
            }
        }
    } finally {
        $archive.Dispose()
    }
} finally {
    $stream.Dispose()
}

$check = [IO.Compression.ZipFile]::OpenRead($zipPath)
try {
    if ($null -eq $check.GetEntry('m360-core/m360-core.php')) {
        throw 'O ZIP não contém o identificador canônico m360-core/m360-core.php.'
    }
    if ($check.Entries | Where-Object { $_.FullName.Contains('\') }) {
        throw 'O ZIP contém caminhos incompatíveis com servidores Linux.'
    }
} finally {
    $check.Dispose()
}

$hash = Get-FileHash -Algorithm SHA256 -LiteralPath $zipPath
[PSCustomObject]@{
    Version = $Version
    Package = $zipPath
    SHA256 = $hash.Hash
}