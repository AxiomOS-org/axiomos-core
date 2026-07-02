$files = Get-ChildItem -Path 'app','modules','tests' -Recurse -File -Filter *.php
$failed = $false
foreach ($file in $files) {
    php -l $file.FullName | Out-Null
    if ($LASTEXITCODE -ne 0) { $failed = $true }
}
if ($failed) { exit 1 }
