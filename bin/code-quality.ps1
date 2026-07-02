param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
)

$reports = Join-Path (Split-Path $PSScriptRoot -Parent) 'storage\reports'
if (-not (Test-Path $reports)) { New-Item -ItemType Directory -Path $reports -Force | Out-Null }

Write-Host '==> Code quality scan (PHPUnit QA subset)'
powershell -ExecutionPolicy Bypass -File "$PSScriptRoot\test.ps1" --testsuite QA --filter QaScorecardTest
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host 'Code quality report generated.'
exit 0
