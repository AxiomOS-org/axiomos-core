# 005 Service Rules

## Service Responsibilities
- Implement use-case orchestration.
- Enforce transactional boundaries.
- Coordinate repositories, domain services, and platform hooks.

## Rules
- Controllers must call services, never repositories directly.
- Services must be deterministic and side-effect explicit.
- Service methods should model business intent, not CRUD verbs only.
- Service responses should use domain objects or DTOs consistently.

## Transactions
- Multi-write operations must run in explicit transactions.
- Failures must throw typed exceptions with actionable context.
