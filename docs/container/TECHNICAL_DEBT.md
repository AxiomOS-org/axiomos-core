# TECHNICAL DEBT — Service Container (Sprint 4.5)

## Scope

Post-Sprint 4.5 known gaps before Sprint 4.6.

## Findings

| ID | Item | Priority |
|---|---|---|
| TD-4.5-01 | No compiled/AOT container — reflection on every transient auto-wire | Medium |
| TD-4.5-02 | Binding cache excludes closures and deferred provider bodies | Medium |
| TD-4.5-03 | No method/parameter attribute injection (`#[Inject]`) | Low |
| TD-4.5-04 | `buildClass()` is public for lazy closures — should be internalised | Low |
| TD-4.5-05 | No mutation testing pipeline yet (target ≥ 80% before Sprint 5) | High |
| TD-4.5-06 | Code coverage tooling not wired in CI (target line ≥ 90%, branch ≥ 85%) | High |
| TD-4.5-07 | Service discovery is explicit (`withProviders`) — no filesystem PSR-4 scan yet | Medium |
| TD-4.5-08 | `ConfigurationManager` still minimal stub — full impl Sprint 4.6 | High |
| TD-4.5-09 | Duplicate singular/plural modules (`User`/`Users`) still in `/modules` | Medium |
| TD-4.5-10 | Laravel 12 bootstrap not started — container not yet wired to real HTTP | High |

## Risks

- **Coverage gap** — without CI enforcement, regressions in circular-dependency detection or deferred providers may slip through.
- **Stub configuration** — kernel boots with in-memory config only; production env layering missing.
- **Public `buildClass()`** — could be misused to bypass binding policies.

## Recommendations

1. Wire **PHPUnit coverage** + thresholds in CI before Sprint 5.
2. Add **Infection** mutation testing in Sprint 4.5.1 or early Sprint 5.
3. Replace `ConfigurationManager` stub in Sprint 4.6 without changing `Kernel` API.
4. Internalise `buildClass()` behind a `Resolver` class in a refactor sprint.
5. Add filesystem provider discovery when marketplace lands.

## Status

**Pass** — acceptable debt for infrastructure sprint; **must resolve TD-4.5-05, TD-4.5-06, TD-4.5-08, TD-4.5-10** before Sprint 5 Authentication.
