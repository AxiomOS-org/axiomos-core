param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
)

$reports = Join-Path (Split-Path $PSScriptRoot -Parent) 'storage\reports'
if (-not (Test-Path $reports)) { New-Item -ItemType Directory -Path $reports -Force | Out-Null }

Write-Host '==> Infection mutation testing'
vendor\bin\infection --threads=4 --only-covered --min-msi=45 --min-covered-msi=40 @Args *> (Join-Path $reports 'infection-console.log')

if ($LASTEXITCODE -ne 0) {
    Write-Host 'Mutation testing failed or coverage driver unavailable.'
    exit $LASTEXITCODE
}

Write-Host 'Mutation testing passed.'
exit 0
