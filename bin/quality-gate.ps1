$steps = @(
    @{ Name='Runtime Validation'; Command='composer runtime:test' },
    @{ Name='Stability Validation'; Command='composer stability:test' },
    @{ Name='Browser Validation'; Command='composer browser:test' },
    @{ Name='Production Validation'; Command='composer production:test' },
    @{ Name='Architecture Validation'; Command='composer architecture:test' },
    @{ Name='Performance Validation'; Command='composer performance:test' },
    @{ Name='Security Validation'; Command='composer security:test' },
    @{ Name='Reliability Validation'; Command='composer reliability:test' },
    @{ Name='QA Validation'; Command='composer qa:test' },
    @{ Name='Lint'; Command='composer lint' },
    @{ Name='Static Analysis'; Command='composer static-analysis' },
    @{ Name='Unit Tests'; Command='composer test:unit' },
    @{ Name='Module Tests'; Command='composer test:module' },
    @{ Name='Integration Tests'; Command='composer test:integration' }
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
