[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [ValidateScript({ Test-Path -LiteralPath $_ })]
    [string] $PackagePath
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$resolvedPackage = (Resolve-Path -LiteralPath $PackagePath).Path
$temporaryDirectory = $null

try {
    if ([System.IO.Path]::GetExtension($resolvedPackage) -ieq '.zip') {
        $temporaryDirectory = Join-Path ([System.IO.Path]::GetTempPath()) ("m360-dw-verify-" + [Guid]::NewGuid().ToString('N'))
        New-Item -ItemType Directory -Path $temporaryDirectory -Force | Out-Null
        Expand-Archive -LiteralPath $resolvedPackage -DestinationPath $temporaryDirectory -Force
        $packageDirectory = $temporaryDirectory
    } elseif (Test-Path -LiteralPath $resolvedPackage -PathType Container) {
        $packageDirectory = $resolvedPackage
    } else {
        throw 'Informe o arquivo ZIP produzido pelo exportador ou a pasta extraída.'
    }

    $manifestPath = Get-ChildItem -LiteralPath $packageDirectory -Filter 'manifest.json' -File -Recurse |
        Select-Object -First 1 -ExpandProperty FullName
    if (-not $manifestPath) {
        throw 'manifest.json não encontrado no pacote.'
    }

    $manifestDirectory = Split-Path -Parent $manifestPath
    $manifest = Get-Content -LiteralPath $manifestPath -Raw | ConvertFrom-Json
    $errors = New-Object System.Collections.Generic.List[string]

    foreach ($file in $manifest.files) {
        if ([System.IO.Path]::GetFileName([string] $file.name) -ne [string] $file.name) {
            $errors.Add("Nome de arquivo inválido no manifesto: $($file.name)")
            continue
        }

        $filePath = Join-Path $manifestDirectory $file.name
        if (-not (Test-Path -LiteralPath $filePath -PathType Leaf)) {
            $errors.Add("Arquivo ausente: $($file.name)")
            continue
        }

        $actualHash = (Get-FileHash -LiteralPath $filePath -Algorithm SHA256).Hash.ToLowerInvariant()
        if ($actualHash -ne ([string] $file.sha256).ToLowerInvariant()) {
            $errors.Add("SHA-256 divergente: $($file.name)")
        }

        if ((Get-Item -LiteralPath $filePath).Length -ne [long] $file.bytes) {
            $errors.Add("Tamanho divergente: $($file.name)")
        }
    }

    $sqlFiles = @(Get-ChildItem -LiteralPath $manifestDirectory -Filter '*.sql' -File)
    $forbiddenPatterns = @(
        '(?im)^\s*CREATE\s+USER\b',
        '(?im)^\s*ALTER\s+USER\b',
        '(?im)^\s*GRANT\b',
        '(?im)^\s*REVOKE\b',
        '(?im)IDENTIFIED\s+(?:BY|WITH)\b',
        '(?im)\bmysql\.(?:user|global_priv)\b',
        '(?im)\bwp_users\b',
        '(?im)\bwp_usermeta\b'
    )

    foreach ($sqlFile in $sqlFiles) {
        $reader = [System.IO.File]::OpenText($sqlFile.FullName)
        try {
            while (-not $reader.EndOfStream) {
                $line = $reader.ReadLine()
                foreach ($pattern in $forbiddenPatterns) {
                    if ($line -match $pattern) {
                        $errors.Add("Conteúdo não permitido detectado em $($sqlFile.Name): $pattern")
                    }
                }
            }
        } finally {
            $reader.Dispose()
        }
    }

    if ($errors.Count -gt 0) {
        $errors | ForEach-Object { Write-Error $_ -ErrorAction Continue }
        throw "Pacote reprovado com $($errors.Count) ocorrência(s)."
    }

    Write-Host 'Pacote validado.' -ForegroundColor Green
    Write-Host "Origem lógica: $($manifest.sourceLabel)"
    Write-Host "Gerado em UTC: $($manifest.generatedAtUtc)"
    Write-Host "Arquivos SQL:  $($sqlFiles.Count)"
    Write-Host "Cliente:       $($manifest.dumpClient)"
} finally {
    if ($temporaryDirectory -and (Test-Path -LiteralPath $temporaryDirectory)) {
        Remove-Item -LiteralPath $temporaryDirectory -Recurse -Force
    }
}
