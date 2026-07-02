# ADR — Enterprise Service Container (Sprint 4.5)

## Status

Accepted — 2026-07-02

## Context

The minimal `ServiceContainer` stub from Sprint 4.4 could not support module service providers, auto-wiring, observability hooks, or PSR-11 compliance. Sprint 4.5 delivers the production DI container that every kernel component, module, and HTTP entrypoint will depend on.

## Decision

### Single `Container` implementing PSR-11 + enterprise API

One class (`Container`) owns resolution, lifetime management, provider orchestration, and event emission. `ContainerBuilder` constructs pre-configured instances for bootstrap and tests.

### Lifetime scopes via `Scope` enum

| Scope | Behaviour |
|---|---|
| `Transient` | New instance every resolution |
| `Singleton` | One instance per container |
| `Scoped` | One instance per scope; flushed via `flushScoped()` |

### Reflection-based auto-wiring

Constructor dependencies are resolved by type-hint. Circular dependencies are detected via a resolution stack and throw `CircularDependencyException`.

### Provider model

| Type | Interface | Behaviour |
|---|---|---|
| Standard | `ServiceProviderInterface` | `register()` called immediately |
| Bootable | `BootableProviderInterface` | `boot()` after all registrations |
| Deferred | `DeferredProviderInterface` | `register()` on first `provides()` hit |

### Observability hooks

`ContainerResolving`, `ContainerResolved`, `ContainerFailed` events feed future Prometheus/OpenTelemetry exporters.

### Binding metadata cache

`cache()` / `loadCache()` persist serialisable binding metadata (class-string concretes only) to a PHP return file for faster cold boots. Closures are excluded from cache.

### Kernel integration

`Kernel` type-hints `ContainerInterface`. `KernelInterface` contract added; `KernelManager` depends on `KernelInterface` only.

## Alternatives rejected

| Alternative | Why rejected |
|---|---|
| Laravel Container as-is | Couples kernel to full framework; harder to optimise for Octane/RoadRunner |
| PHP-DI / Symfony DI wholesale | External dependency; less control over AxiomOS-specific provider/deferred semantics |
| Service locator anti-pattern | Violates SOLID; explicit constructor injection only |
| Runtime eval for cache | Security risk; PHP return files are safer |

## Trade-offs

| Benefit | Cost |
|---|---|
| Full DI with PSR-11 | Reflection overhead on cold auto-wire paths |
| Deferred providers | First resolution latency includes registration |
| Event hooks on every resolve | Measurable overhead; acceptable for enterprise observability |
| Binding cache | Cannot cache closure bindings; partial cache only |

## Future extension points

- Sprint 4.6 — `ConfigurationManager` bound as singleton via provider
- Sprint 4.7 — `EventBus` wired through `ContainerResolved` hooks
- Sprint 4.8 — HTTP kernel resolves controllers via `call()`
- Attribute-based injection (`#[Inject]`)
- Compiled container (ahead-of-time resolution graph)
- Mutation testing gate (≥ 80% target before Sprint 5)

## Files

```
app/Core/Container/Container.php
app/Core/Container/ContainerBuilder.php
app/Core/Container/ServiceProvider.php
app/Core/Container/Binding.php
app/Core/Container/Scope.php
app/Core/Container/Contracts/*
app/Core/Container/Events/*
app/Core/Container/Exceptions/*
app/Core/Kernel/Contracts/KernelInterface.php
tests/Feature/Core/Container/ContainerTest.php
tests/Benchmark/Container/ContainerBenchmarkTest.php
```

## Test evidence

89 tests, 214 assertions — all passing.
