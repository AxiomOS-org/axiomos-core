param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
)

$ErrorActionPreference = 'Continue'
$reports = Join-Path (Split-Path $PSScriptRoot -Parent) 'storage\reports'

if (-not (Test-Path $reports)) {
    New-Item -ItemType Directory -Path $reports -Force | Out-Null
}

Write-Host '==> PHPStan (level 6 + baseline)'
& vendor\bin\phpstan analyse -c phpstan.neon --memory-limit=512M --no-progress --error-format=table 2>&1 | Tee-Object -FilePath (Join-Path $reports 'phpstan.log') | Out-Null
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host '==> Psalm (level 4)'
& vendor\bin\psalm --no-cache --no-progress 2>&1 | Tee-Object -FilePath (Join-Path $reports 'psalm.log') | Out-Null
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host '==> Rector dry-run'
$rectorLog = Join-Path $reports 'rector-dry-run.log'
& vendor\bin\rector process --dry-run --no-progress-bar 2>&1 | Tee-Object -FilePath $rectorLog | Out-Null
if ($LASTEXITCODE -ge 2) { exit $LASTEXITCODE }

Write-Host 'Static analysis passed.'
exit 0
