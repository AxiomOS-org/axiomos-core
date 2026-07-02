param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
)

$reports = Join-Path (Split-Path $PSScriptRoot -Parent) 'storage\reports'
if (-not (Test-Path $reports)) { New-Item -ItemType Directory -Path $reports -Force | Out-Null }

Write-Host '==> PHP syntax lint'
powershell -ExecutionPolicy Bypass -File "$PSScriptRoot\lint.ps1"
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host '==> Architecture file rules'
powershell -ExecutionPolicy Bypass -File "$PSScriptRoot\architecture-rules.ps1"
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host '==> Architecture PHPUnit suite'
powershell -ExecutionPolicy Bypass -File "$PSScriptRoot\test.ps1" --testsuite Architecture @Args
exit $LASTEXITCODE
