# ADR — Enterprise Configuration Manager (Sprint 4.6)

## Status

Accepted — 2026-07-02

## Context

The kernel required layered configuration (env, files, database, modules, plugins, runtime) with validation, caching, reload, and observability — the last core component before Event Bus and HTTP integration.

## Decision

### Layered loaders with explicit precedence

| Priority | Source | Loader |
|---|---|---|
| 10 | File | `FileConfigurationLoader` |
| 20 | Environment | `EnvironmentConfigurationLoader` |
| 30 | Database | `DatabaseConfigurationLoader` |
| 40 | Module | `ModuleConfigurationLoader` |
| 50 | Plugin | `PluginConfigurationLoader` |
| 60 | Runtime | `RuntimeConfigurationLoader` |

Later layers override earlier layers via `array_replace_recursive`.

### `ConfigurationBuilder` for bootstrap

Kernel and HTTP entrypoints construct the manager through `ConfigurationBuilder::create($basePath)` — no manual `new ConfigurationManager()` in application code.

### Dot-notation access

`ArrayPath` provides `get` / `set` / `has` for nested keys (`app.name`).

### Validation at load time

`ConfigurationValidator` enforces required keys and allowed values before `ConfigurationLoaded` fires.

### Cache format

PHP return file storing merged items + source map. Closures and DB live queries are never cached.

## Alternatives rejected

| Alternative | Why rejected |
|---|---|
| vlucas/phpdotenv dependency | Avoid extra dependency; simple parser sufficient for MVP |
| Single monolithic config file | Cannot scale to modules/plugins/marketplace |
| Environment-only (12-factor only) | ERP needs DB + module overrides |
| Reload without validation | Unsafe for production hot reload |

## Trade-offs

| Benefit | Cost |
|---|---|
| Full layer separation | Merge + source tracking on every reload |
| Module/plugin config scan | Filesystem I/O on cold load (mitigated by cache) |
| Runtime overrides | Must not expose `set()` to HTTP layer |

## Future extension points

- Encrypted secrets layer (Vault/KMS)
- Remote config (Consul/etcd) as new loader
- Schema-based validation (JSON Schema)
- Configuration versioning + audit trail

## Test evidence

99+ tests — all passing after Sprint 4.6 integration.
