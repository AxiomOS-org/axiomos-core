# 016 Performance Rules

## Performance Targets
- Define SLA/SLO per critical endpoint and job.
- Track P50/P95/P99 for API latency.

## Rules
- Avoid unbounded queries in application code.
- Use pagination by default for list views.
- Detect and fix N+1 query patterns before release.
- Profile expensive workflows with repeatable benchmarks.

## Gates
- Performance smoke suite is required for merge.
