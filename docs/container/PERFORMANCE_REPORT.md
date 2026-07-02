# PERFORMANCE REPORT — Service Container (Sprint 4.5)

## Scope

`App\Core\Container\Container` resolution hot paths.

## Benchmarks

| Scenario | Iterations | Budget | Result |
|---|---|---|---|
| Singleton resolution (cached) | 5,000 | < 500 ms | **Pass** |
| Auto-wired transient resolution | 5,000 | < 2,000 ms | **Pass** |

Run: `vendor/bin/phpunit --group benchmark`

## Findings

1. **Singleton hits are O(1)** — resolved instances stored in `$instances`; subsequent `make()` returns without reflection.
2. **Auto-wiring uses reflection per transient resolve** — expected cost; mitigated by binding concrete classes as singletons in production modules.
3. **Event dispatch adds overhead** — optional `Dispatcher`; omit in benchmark path for max throughput when needed.
4. **Deferred providers defer cost** — first touch pays registration + resolution; subsequent touches are normal.
5. **Binding cache** — `loadCache()` skips re-parsing `module.json`-equivalent binding setup; closures not cached.

## Risks

| Risk | Severity | Mitigation |
|---|---|---|
| Reflection on every transient resolve | Medium | Prefer singleton/scoped for heavy services |
| Event hook on every resolve | Low | Make dispatcher optional (already is) |
| Large tagged service groups | Low | Lazy-bind expensive tagged services |
| Cold boot with 100+ auto-wired classes | Medium | Use `cache()` in production bootstrap |

## Recommendations

1. Bind module service providers as **singleton** where possible.
2. Enable **binding cache** in production (`ContainerBuilder::withCache($path, loadOnBuild: true)`).
3. Add **compiled container** in Sprint 4.5.1 if auto-wire P99 exceeds budget under load test.
4. Track `ContainerResolved` metrics in OpenTelemetry before Sprint 5.

## Status

**Pass** — within benchmark budgets on PHP 8.3.16 / Windows dev machine.
