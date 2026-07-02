param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
)

powershell -ExecutionPolicy Bypass -File "$PSScriptRoot\test.ps1" --testsuite QA @Args
exit $LASTEXITCODE
