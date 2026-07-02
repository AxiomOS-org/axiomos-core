param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
)

$reports = Join-Path (Split-Path $PSScriptRoot -Parent) 'storage\reports'
if (-not (Test-Path $reports)) { New-Item -ItemType Directory -Path $reports -Force | Out-Null }

Write-Host '==> PHPUnit coverage (text)'
powershell -ExecutionPolicy Bypass -File "$PSScriptRoot\test.ps1" --coverage-text --coverage-filter app --coverage-filter modules --testsuite Unit,Module,Integration,Architecture,Stability,Security,Performance,Reliability,QA @Args *> (Join-Path $reports 'coverage.txt')

if ($LASTEXITCODE -ne 0) {
    Write-Host 'Coverage run failed (enable Xdebug or PCOV for coverage metrics).'
    exit $LASTEXITCODE
}

Write-Host 'Coverage report written to storage/reports/coverage.txt'
exit 0
