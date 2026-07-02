# ADT Simulation Mode

## Mandatory Rule
Generators must **never** modify files directly without simulation and approval.

This rule implements **ADT Rule 0** (`ADT_RULE_ZERO.md`): no AI or generator writes production code without developer approval.

## Pipeline
1. **Generate** — build artifact plan in memory
2. **Preview** — show diff/preview of proposed changes
3. **Impact Analysis** — affected modules, dependencies, migrations
4. **Dependency Analysis** — cross-module and platform impact
5. **Conflict Detection** — naming, path, manifest conflicts
6. **Approval** — human or policy gate
7. **Write Files** — apply only after approval
8. **Tests** — run generated/relevant test suites
9. **Documentation** — update docs bundle
10. **Quality Gates** — lint, static analysis, architecture rules

## Phase 5.A.4B Scope
Only `axiomos:make-module` must implement simulation-first flow.

## Failure Handling
- Simulation failures block write phase.
- All simulation artifacts are logged for audit.
