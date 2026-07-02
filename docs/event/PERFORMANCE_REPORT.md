# PERFORMANCE REPORT — Event Bus (Sprint 4.7)

## Benchmarks

Run: `vendor/bin/phpunit --testsuite Benchmark`

| Scenario | Iterations | Budget | Result |
|---|---|---|---|
| Single-listener dispatch | 10,000 | < 1,500 ms | **Pass** |
| Wildcard dispatch (10 listeners) | 10,000 | < 3,000 ms | **Pass** |

## Findings

1. **Exact-match lookup is O(1)** via event-name index.
2. **Wildcard listeners are scanned per dispatch** — O(w) where w = wildcard count. Keep wildcard listeners few.
3. **`usort` per dispatch** orders matched registrations — O(k log k) on matched count k, negligible for typical k.
4. **Meta-events add 2 dispatches per domain dispatch** (Before/After) through the pure dispatcher — measured within budget.
5. **Generator-based listener iteration** keeps once-removal lazy and correct under propagation stop.

## Risks

| Risk | Severity | Mitigation |
|---|---|---|
| Many wildcard listeners | Medium | Prefer exact registrations; document guidance |
| Large history buffer memory | Low | Bounded ring buffer (default 1000) |
| Synchronous queue draining on request path | Medium | Drain via worker/scheduler, not per request |

## Recommendations

1. Register hot-path listeners by exact event name.
2. Drain the queue from a scheduler/worker tick (Octane/RoadRunner), not inline.
3. Cache class-based listener maps in production (`cache()`).
4. Export `EventMetrics` to OpenTelemetry before Sprint 5.

## Status

**Pass**
