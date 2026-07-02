# 004 DDD Rules

## Domain Layer Contains Only
- Entities
- Value Objects
- Domain Services
- Repository Interfaces
- Domain Events

## Domain Restrictions
- No framework-specific HTTP, DB, routing, or view dependencies.
- No direct container or facade usage.
- No infrastructure implementation classes.

## Application Boundary
- Domain behavior is orchestrated via use cases in `Application/`.
- Cross-domain communication uses events/contracts, not direct deep coupling.
