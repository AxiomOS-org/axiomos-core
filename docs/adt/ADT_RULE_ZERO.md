# Rule 0 — No AI Generates Production Code Directly

## Policy
ADT and Cursor (or any AI assistant) must **never** write production code directly without passing through the governed pipeline.

## Mandatory Pipeline
```
Business Requirement
        │
        ▼
Requirement Analyzer
        │
        ▼
Architecture Validator
        │
        ▼
DDD Validator
        │
        ▼
Simulation
        │
        ▼
Preview
        │
        ▼
Developer Approval
        │
        ▼
File Generation
        │
        ▼
Quality Gates
        │
        ▼
Tests
        │
        ▼
Documentation
        │
        ▼
Ready
```

## Enforcement
- No generator may skip simulation or approval.
- AI output is advisory until validated and approved by a developer.
- Sprint 5.A.4C self-validation is required before business module development.

## Governance
- ADR-011
- `ADT_SIMULATION_MODE.md`
- `MODULE_BLUEPRINT_SPEC.md`
