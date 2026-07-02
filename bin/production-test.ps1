param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
)

powershell -ExecutionPolicy Bypass -File "$PSScriptRoot\test.ps1" --testsuite Production @Args
exit $LASTEXITCODE
