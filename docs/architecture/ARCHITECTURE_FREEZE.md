# AxiomOS Architecture Freeze

## Freeze Metadata
- Freeze Date: 2026-07-02
- Freeze Version: AF-1.0
- Approved By: Project Architect
- Scope: Core platform architecture governance before business development.

## Frozen Components
- Folder Structure
- Module Structure
- Kernel
- Boot Process
- Container
- Configuration
- Event Bus
- Platform Layer
- Dependency Rules

## Change Policy
- No direct modifications to frozen components without an approved ADR.
- ADR must include:
  - Problem statement
  - Proposed change
  - Alternatives
  - Risk/rollback
  - Compatibility impact
- Emergency patches are allowed only for production incidents and must be backfilled by ADR within 24 hours.

## Enforcement
- PRs touching frozen components must reference approved ADR IDs.
- CI architecture checks block merge if ADR reference is missing.

