# Demo Testing Guide

## Suites
- `Tests/Unit` for pure domain/application logic
- `Tests/Feature` for module HTTP and integration flows
- `Tests/Integration` for persistence and external adapters

## Commands
```bash
composer test:module
composer quality:gate
```

## Minimum Gate
- Module smoke test passes
- Architecture rules pass
- No Domain-layer Eloquent repositories
