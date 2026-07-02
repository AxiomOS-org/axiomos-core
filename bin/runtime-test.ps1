param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
)

powershell -ExecutionPolicy Bypass -File "$PSScriptRoot\test.ps1" --testsuite Runtime @Args
exit $LASTEXITCODE
