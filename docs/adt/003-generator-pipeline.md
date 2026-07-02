# 003 Generator Pipeline

## Pipeline Stages
1. Parse input specification.
2. Validate against standards/governance.
3. Build generation plan.
4. Render templates.
5. Apply code transformers.
6. Run quality checks.
7. Generate docs and reports.

## Failure Policy
- Fail-fast on architecture violations.
- Collect all template validation errors.
- Produce actionable diagnostics with remediation hints.
