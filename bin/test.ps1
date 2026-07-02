param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
)

$php = (Get-Command php -ErrorAction Stop).Source
$isWindows = $env:OS -eq 'Windows_NT'

if ($isWindows) {
    $extensionDirectory = Join-Path (Split-Path $php -Parent) "ext"

    & $php `
        -d "extension_dir=$extensionDirectory" `
        -d "extension=php_pdo_pgsql.dll" `
        -d "extension=php_pgsql.dll" `
        vendor/bin/phpunit `
        @Args
} else {
    & $php vendor/bin/phpunit @Args
}

exit $LASTEXITCODE
