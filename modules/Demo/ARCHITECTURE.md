# Demo Architecture

## Layering
This module follows AxiomOS DDD + hexagonal architecture:

- **Domain**: entities, value objects, repository contracts, policies
- **Application**: services, DTOs, orchestration
- **Infrastructure**: persistence adapters and external integrations
- **Presentation/API**: delivery mechanisms only

## Dependencies
- Core module
- Platform services (`app/Platform`)

## Boundaries
- No persistence logic in Domain
- No business rules in Presentation/API
- Cross-module integration via events/contracts only

## ADT Governance
Generated under Rule 0 (simulation-first, developer approval required).
