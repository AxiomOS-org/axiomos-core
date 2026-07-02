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

### Mandatory Flow (Rule 0)
1. Simulation
2. Preview
3. Developer approval
4. Module blueprint file generation
5. Quality gates
6. Tests
7. Documentation bundle verification

### Blueprint Deliverables
- AMS `module.json`
- Mandatory docs: README, ARCHITECTURE, CHANGELOG, TESTING
- Recommended docs: ROADMAP, TODO
- Standard module directories per `MODULE_BLUEPRINT_SPEC.md`

## Phase 5.A.4C (Self-Validation)
- Generate `Demo` module via ADT
- Validate structure, standards, tests, docs, quality gates
- Declare ADT production-ready before Sprint 5.B

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
