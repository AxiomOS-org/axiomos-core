# 003 Generator Pipeline

## Pipeline Stages
1. Parse input specification.
2. Load domain knowledge pack (if domain module).
3. Validate against standards/governance.
4. Build generation plan (simulation mode).
5. Preview proposed artifacts.
6. Run impact, dependency, and conflict analysis.
7. Require approval gate.
8. Render templates and write files.
9. Apply code transformers.
10. Generate tests and documentation.
11. Run quality checks and sprint reports.

## Simulation-First Enforcement
Generators must never modify files before approval.

Required sequence:
Generate -> Preview -> Impact Analysis -> Dependency Analysis -> Conflict Detection -> Approval -> Write Files

See `ADT_SIMULATION_MODE.md`.

## Failure Policy
- Fail-fast on architecture violations.
- Collect all template validation errors.
- Produce actionable diagnostics with remediation hints.
- Block write phase on unresolved conflicts.

## Phase 5.A.4B Rule
Only `axiomos:make-module` is implemented first, but it must follow the full simulation-first pipeline.
