param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
)

$phpunitArgs = @('--testsuite', 'Module', '--exclude-group', 'certification')
if ($Args.Count -gt 0 -and $Args[0] -ne '') {
    $phpunitArgs += @('--filter', [regex]::Escape($Args[0]))
}

powershell -ExecutionPolicy Bypass -File "$PSScriptRoot\test.ps1" @phpunitArgs
exit $LASTEXITCODE
