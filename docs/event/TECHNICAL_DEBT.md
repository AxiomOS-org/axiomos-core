# TECHNICAL DEBT — Event Bus (Sprint 4.7)

## Findings

| ID | Item | Priority |
|---|---|---|
| TD-4.7-01 | In-memory queue/store are single-process only | High (before cross-process async) |
| TD-4.7-02 | No exponential backoff on retry (immediate re-enqueue) | Medium |
| TD-4.7-03 | Small dispatch-loop duplication (pure `EventDispatcher` vs instrumented `EventBus`) | Low |
| TD-4.7-04 | `EventDiscovery` registers subscribers only; no attribute-based listener scan | Medium |
| TD-4.7-05 | Cache covers class-based listeners only (closures excluded by design) | Low |
| TD-4.7-06 | History stores error string, not structured context | Low |
| TD-4.7-07 | CI coverage/mutation gates still not enforced (line ≥ 90%, branch ≥ 85%, mutation ≥ 80%) | High |
| TD-4.7-08 | Not yet wired into Kernel/Container as the platform dispatcher | High (Sprint 4.8) |

## Recommendations

1. Add Redis/database queue + persistent store before modules rely on async.
2. Add exponential backoff and dead-letter handling.
3. Wire `EventBus` into the container and kernel in Sprint 4.8 (replace `Illuminate` dispatcher usage in core emitters over time).
4. Enforce coverage/mutation gates before Sprint 5 Authentication.

## Status

**Pass** — acceptable for infrastructure completion; resolve TD-4.7-01, TD-4.7-07, TD-4.7-08 before/at Sprint 4.8.
