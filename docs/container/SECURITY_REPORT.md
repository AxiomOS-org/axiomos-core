# SECURITY REPORT — Service Container (Sprint 4.5)

## Scope

`App\Core\Container\Container` and binding cache mechanism.

## Findings

1. **PSR-11 `get()` / `make()` resolve arbitrary string identifiers** — if user-controlled strings reach `make()` without an allowlist, unintended classes could be instantiated. Kernel and modules must never pass request input directly to `make()`.
2. **Binding cache uses `require`** — `loadCache()` executes a PHP file from disk. Cache path must be a trusted, non-world-writable directory (e.g. `storage/framework/cache`).
3. **Auto-wiring instantiates any resolvable class** — `class_exists()` enables resolution without explicit binding. Only autoloaded application classes should be reachable; no user namespace input.
4. **Closures in bindings** — factory closures can execute arbitrary code at resolve time. Only trusted providers may register closures.
5. **No serialisation of resolved objects in cache** — good; prevents object injection via cache payload.
6. **Circular dependency detection** — prevents infinite recursion / stack exhaustion DoS from misconfigured graphs.

## Risks

| Risk | Severity | Likelihood |
|---|---|---|
| User input → `make($class)` | **High** if misused | Low (kernel does not expose) |
| Poisoned cache file | **High** | Low (requires filesystem write access) |
| Closure injection via malicious provider | **High** | Low (marketplace signing future) |
| Reflection bypass of private constructors | Medium | Low (ReflectionClass respects `isInstantiable()`) |

## Recommendations

1. **Never** pass HTTP/request parameters to `make()` or `get()`.
2. Restrict cache directory permissions to the PHP process user only.
3. Validate module provider classes against an allowlist before `registerProvider()` (marketplace sprint).
4. Add `ContainerInterface::make()` audit logging in production for non-core namespaces.
5. Sign binding cache files with HMAC in a future hardening sprint.

## Status

**Pass** — no known vulnerabilities in intended usage (trusted providers, trusted cache path, no user-controlled abstracts). Residual risk is **misuse at integration boundaries** (HTTP layer, marketplace).
