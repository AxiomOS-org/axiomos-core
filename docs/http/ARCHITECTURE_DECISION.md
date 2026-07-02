# ADR — HTTP Kernel Integration (Sprint 4.8)

## Status

Accepted — 2026-07-02

## Context

The AxiomOS core was a kernel library with no HTTP entrypoint. Sprint 4.8 delivers the milestone: a browser hitting the app boots the kernel, loads modules and returns a ready status. It must integrate Laravel's HTTP layer with the AxiomOS kernel and expose `/` and `/health`.

## Decision

### HTTP stack: Illuminate routing + HTTP (not full `laravel/framework`)

We added `illuminate/routing` and `illuminate/http` only. The full framework/foundation would drag in config, providers, facades and a second container that duplicates the AxiomOS kernel. Using the router + request/response keeps the core lean and keeps AxiomOS in control of the lifecycle.

### Per-request lifecycle

`HttpKernel::handle()` implements the documented flow:

```
Load configuration -> Boot kernel -> Load modules -> Dispatch events -> Return response
```

`KernelManager::boot()` performs config load + module discovery/boot; the HTTP kernel boots lazily and **reuses** a ready kernel. FPM boots once per process; Octane/RoadRunner boot once per worker.

### Two containers, one kernel

The Laravel `Router` uses its own `Illuminate\Container\Container` (for `CallableDispatcher`/`ControllerDispatcher`), while modules and services live in the AxiomOS `Container`. They are intentionally separate: the router container is an HTTP implementation detail, the AxiomOS container is the domain runtime. Controllers receive AxiomOS services via constructor injection from the factory.

### Endpoints

| Route | Response |
|---|---|
| `GET /` | `text/plain` — `AxiomOS Kernel Booted Successfully` |
| `GET /health` | canonical JSON (`kernel`, `status`, `version`, `modules`, `bootTime`, `memory`) + `checks[]` |
| `GET /metrics` | kernel + boot metrics JSON |

Unknown routes return `404` JSON; unhandled errors return `500` JSON — the kernel never leaks stack traces.

### Health checks

A dedicated subsystem (`HealthChecker` + `HealthCheckInterface`) aggregates independent probes (`KernelReadyCheck`, `ModulesBootedCheck`, `MemoryCheck`) into a `HealthReport` with a worst-status roll-up. `Down` maps to HTTP 503 (Kubernetes-probe friendly); `Degraded` stays 200.

### HTTP events via the Event Bus (Sprint 4.7)

`HttpKernel` dispatches `RequestReceived` and `ResponsePrepared` through the `EventBus`, exercising the event system per request. Kernel boot events still flow through the Illuminate dispatcher the kernel was built with.

### Modules: 4 enabled core-tier, 16 scaffolded/disabled

All 20 module dirs received a real `ModuleServiceProvider` (under a new `Modules\` PSR-4 mapping), so discovery's strict provider-existence check stays honest. Exactly four core-tier modules are enabled — **Core, Authentication, Authorization, Settings** — yielding the milestone `"modules": 4`. The other sixteen are `enabled: false` (not yet implemented → skipped at boot, not failed). Enabling them later is a one-line manifest change.

A production `ProviderModuleBootstrapper` resolves each enabled module's provider from the AxiomOS container and runs its `register()`/`boot()` phases.

## Alternatives rejected

| Alternative | Why rejected |
|---|---|
| Full `laravel/framework` | Heavy; second container/config system competes with the AxiomOS kernel |
| Permissive provider checker to skip scaffolds | Weakens the core discovery guarantee globally |
| Delete the 16 scaffold modules | Destructive; they are future work |
| Boot kernel fresh on every request even when ready | Wasteful; breaks long-running-runtime reuse |

## Consequences

- The milestone browser response is met exactly.
- Two event dispatchers coexist (Illuminate for kernel boot, EventBus for HTTP) — unification tracked as debt.
- Module discovery is not cached across FPM requests yet — tracked as debt.

## Test evidence

126 tests, 304 assertions passing (10 new HTTP/health tests). Verified live via `php -S` against `/` and `/health`.
