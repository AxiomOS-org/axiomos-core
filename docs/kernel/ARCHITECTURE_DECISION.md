# ADR — AxiomOS Kernel (Sprint 4.4)

## Status

Accepted — 2026-07-02

## Context

AxiomOS needs a production kernel that orchestrates configuration, dependency injection, module discovery, and boot — while exposing a stable surface for RoadRunner, Octane, Swoole, Kubernetes health probes, and future HTTP integration (Sprint 5 target response).

Prior sprints delivered `ModuleLoader`, `ModuleRegistry`, and `BootManager`. Sprint 4.4 wires them into a cohesive kernel lifecycle.

## Decision

### Split `Kernel` and `KernelManager`

- **`Kernel`** owns lifecycle state machine, integration, health, and metrics.
- **`KernelManager`** is the thin public façade consumed by runtimes and HTTP entrypoints.

This mirrors the future rule that all managers expose `boot()`, `shutdown()`, `reload()`, `status()`, `health()`, and `metrics()`.

### Lifecycle phases

```
initialize() → register() → boot() → ready() → shutdown() → reload()
```

`boot()` runs the full chain when called from `cold` or `shutdown`. `reload()` performs `shutdown()` then a cold `boot()`. Double `boot()` without `reload()` is rejected to prevent accidental duplicate provider registration.

### Integration via interfaces

The kernel depends on:

| Dependency | Contract | Implementation sprint |
|---|---|---|
| Module discovery/boot | `BootManager` | 4.3 (done) |
| Module state | `ModuleRegistry` | 4.2 (done) |
| DI | `ServiceContainer` | 4.5 (minimal stub now) |
| Configuration | `ConfigurationManager` | 4.6 (minimal stub now) |
| Events | `Dispatcher` | Illuminate contracts |

Stubs are intentionally minimal so Sprint 4.5/4.6 can replace internals without touching the kernel API.

### Boot resilience

Module boot failures do not abort the kernel. `BootReport` + `BootMetrics` capture outcomes for health (`healthy` flag) and observability export (Prometheus/OpenTelemetry later).

### Module lifecycle contract

`ModuleBootstrapper` defines `initialize()`, `register()`, `boot()`, `ready()`, `shutdown()`. Only `boot()` is invoked today; remaining phases are reserved for marketplace hot-reload and graceful shutdown.

### Priority tiers

Modules share priority values within tiers (Core=1, Business=100, AI=500). Duplicate priorities within a tier are allowed; sort order breaks ties alphabetically by name.

## Alternatives rejected

| Alternative | Why rejected |
|---|---|
| Single god-class combining loader, registry, and boot | Violates SRP; untestable; blocks independent module install/removal |
| Embed permissions/RBAC in kernel | Violates module boundaries; belongs in Authorization module |
| Fail-fast on first module error | One broken plugin would brick the entire ERP; unacceptable for marketplace |
| Unique boot priorities per module | Does not scale to 100+ modules; tiered priorities are operationally simpler |
| Static kernel singleton | Breaks DI, testing, and multi-tenant process models |

## Trade-offs

| Benefit | Cost |
|---|---|
| Partial boot survives module failures | Kernel may start in degraded mode; health endpoint must be monitored |
| Interface-first integration | Temporary stub container/config until Sprints 4.5–4.6 |
| Immutable `BootReport` + metrics | Slightly more objects per boot; negligible vs scan cost |
| Strict double-boot guard | Callers must use `reload()` explicitly |

## Future extension points

- **Sprint 4.5** — Replace `ServiceContainer` stub with full auto-wiring, tagged bindings, and interface resolution.
- **Sprint 4.6** — Replace `ConfigurationManager` stub with layered config (env, files, database, module overrides).
- **Sprint 4.7** — Wire `EventBus` between module events and external subscribers.
- **Sprint 4.8** — HTTP kernel returns `{ kernel, status, modules, bootTime }` from `KernelManager::status()`.
- **Observability** — `BootMetrics::toArray()` and `Kernel::metrics()` designed for Prometheus/OpenTelemetry exporters.
- **Module lifecycle** — BootManager will call `initialize()` → `register()` → `boot()` → `ready()` per module; `shutdown()` on kernel shutdown.
- **Managers rule** — `KernelManager` already exposes the six required manager methods; future managers follow the same contract.

## Files

```
app/Core/Kernel/Kernel.php
app/Core/Kernel/KernelManager.php
app/Core/Kernel/KernelState.php
app/Core/Kernel/Events/{KernelInitializing,KernelInitialized,KernelReady,KernelShutdown}.php
app/Core/Boot/BootMetrics.php          (Sprint 4.3 fixes)
app/Core/Boot/BootReport.php          (rich DTO + rates + serialisation)
app/Core/Boot/Contracts/ModuleBootstrapper.php  (full lifecycle)
tests/Feature/Core/Kernel/KernelTest.php
```

## Test evidence

66 tests, 182 assertions — all passing.
