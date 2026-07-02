# Placeholder static analysis gate (upgrade to PHPStan/Psalm in Sprint 5.A.2)
powershell -ExecutionPolicy Bypass -File "$PSScriptRoot\lint.ps1"
exit $LASTEXITCODE
