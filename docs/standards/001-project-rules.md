# 001 Project Rules

## Purpose
Define non-negotiable engineering rules for AxiomOS Enterprise Platform.

## Development Mode (Active)
- Follow `docs/MASTER_ROADMAP.md` for all sprint sequencing.
- Code-first execution: Design → Code → Tests → Browser Demo → APIs → Doc Updates → Review.
- No documentation-only sprints unless an approved ADR requires it.

## Mandatory
- Backward compatibility for public APIs unless an approved migration plan exists.
- PostgreSQL is the single source of truth for runtime and tests.
- Every sprint must declare scope, out-of-scope, and exit criteria.
- Quality gates (`composer quality:gate`) must pass before sprint close.

## Prohibited
- Unplanned scope expansion during a sprint.
- Hidden framework coupling that bypasses defined module boundaries.
- Direct production data mutations without migration scripts and rollback plan.
- New architecture documentation sprints without human architect approval.

## Compliance
- PR template must reference relevant standards documents.
- CI gates block merges on standards violations.
