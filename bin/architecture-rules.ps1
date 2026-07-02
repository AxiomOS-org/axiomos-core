# Architecture guard: fail if Eloquent repositories are placed in Domain layer.
$violations = Get-ChildItem -Path 'modules' -Recurse -File -Filter 'Eloquent*Repository.php' | Where-Object { $_.FullName -match '\\Domain\\' }
if ($violations.Count -gt 0) {
    Write-Host "Architecture rule violation: Eloquent repository found in Domain layer."
    $violations | ForEach-Object { Write-Host $_.FullName }
    exit 1
}
exit 0
