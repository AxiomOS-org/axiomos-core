param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
)

powershell -ExecutionPolicy Bypass -File "$PSScriptRoot\test.ps1" --testsuite Stability @Args
exit $LASTEXITCODE
