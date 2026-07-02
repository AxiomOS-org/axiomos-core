# TECHNICAL DEBT — HTTP Kernel (Sprint 4.8)

## Findings

| ID | Item | Priority |
|---|---|---|
| TD-4.8-01 | Two event dispatchers coexist (Illuminate for kernel boot, EventBus for HTTP) | High |
| TD-4.8-02 | Module discovery not cached across FPM requests (disk scan per process) | Medium |
| TD-4.8-03 | 16 modules are empty scaffolds (`enabled: false`) with no behaviour yet | Medium |
| TD-4.8-04 | `/health` and `/metrics` are unauthenticated | High (before public prod) |
| TD-4.8-05 | No middleware pipeline (headers, CORS, rate limiting) | Medium |
| TD-4.8-06 | Coverage/mutation gates still not enforced in CI | High |
| TD-4.8-07 | `terminate()` only flushes scoped bindings; no full graceful shutdown wiring for Octane | Medium |
| TD-4.8-08 | Error responses expose exception message outside production guard | Low |

## Recommendations

1. **Unify events**: bridge kernel boot events onto the `EventBus` (adapter implementing `Illuminate\Contracts\Events\Dispatcher`), so there is one event backbone.
2. **Cache discovery**: pass a persistent `CacheRepository` (APCu/file) to `ModuleLoader` for FPM.
3. **Guard privileged endpoints** once Authentication/Authorization land (Sprint 5).
4. **Add a middleware layer** for security headers, CORS and rate limiting.
5. **Enforce coverage/mutation gates** before Sprint 5.
6. **Wire Octane/RoadRunner** entrypoints using the existing `boot()/reload()/shutdown()` lifecycle.

## Status

**Pass** — milestone delivered and production-capable for internal use. Resolve TD-4.8-01, TD-4.8-04 and TD-4.8-06 before public production.
