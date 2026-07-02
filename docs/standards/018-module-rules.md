# 018 Module Rules

## Module Contract
- Every module must provide `module.json` with:
  - `name`
  - `version`
  - `dependencies`
  - `minimumCoreVersion`
  - `provider`

## Rules
- Dependency declarations must be explicit and minimal.
- Module boot must be deterministic and idempotent.
- No hidden cross-module direct coupling; use contracts/events.
- Demo data must never auto-run in production.
