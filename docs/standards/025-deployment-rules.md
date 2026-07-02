# 025 Deployment Rules

## Release Discipline
- Deploy from tagged, reviewed, and gated commits only.
- Rollback plan required for each production release.

## Migration Safety
- Database migrations must be backward compatible where possible.
- Risky migrations require maintenance/runbook approval.

## Runtime Checks
- Health checks must pass post-deploy.
- Critical errors trigger rollback/escalation policy.
