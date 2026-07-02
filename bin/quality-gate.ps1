$steps = @(
    @{ Name='Lint'; Command='composer lint' },
    @{ Name='Static Analysis'; Command='composer static-analysis' },
    @{ Name='Architecture Rules'; Command='composer architecture-rules' },
    @{ Name='Unit Tests'; Command='composer test:unit' },
    @{ Name='Module Tests'; Command='composer test:module' },
    @{ Name='Integration Tests'; Command='composer test:integration' },
    @{ Name='Performance Smoke'; Command='composer test:performance' },
    @{ Name='Security Scan'; Command='composer test:security' }
)

foreach ($step in $steps) {
    Write-Host "==> $($step.Name)"
    Invoke-Expression $step.Command
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Quality gate failed at: $($step.Name)"
        exit $LASTEXITCODE
    }
}

Write-Host 'All quality gates passed.'
exit 0
