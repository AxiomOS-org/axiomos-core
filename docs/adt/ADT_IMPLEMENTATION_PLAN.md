# ADT Implementation Plan

## Phase 5.A.4A (Specification) — COMPLETE
- ADT docs `001-040`
- Simulation mode specification
- AMS specification lock
- AI Orchestrator architecture lock
- Knowledge base scaffold (`docs/knowledge/`)
- Prompt library scaffold (`docs/prompts/`)
- Sprint governance report template

## Phase 5.A.4B (Initial Implementation)
Implement only:
```bash
php artisan axiomos:make-module Accounting
```

### Mandatory Flow
1. Simulation
2. Preview
3. Approval
4. File generation
5. Tests
6. Documentation
7. Quality gates

### Constraints
- No direct file writes before approval.
- Output must include AMS-compliant `module.json`.
- Must pass architecture and quality gates.
- No additional generators in this phase.

## Phase 5.B+
- Identity module (first business module)
- Membership, Tenant Context, Authentication, RBAC
- Additional ADT generators incrementally

## Implementation Principles
- Small increments.
- Contract-first (AMS).
- Test-first for generator behavior.
- Backward compatibility for generated outputs.
- Knowledge-base and prompt-library compliance for AI-assisted generation.
