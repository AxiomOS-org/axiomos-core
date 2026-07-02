# 001 Project Rules

## Purpose
Define non-negotiable engineering rules for AxiomOS Enterprise Platform.

## Mandatory
- Architecture-first: no feature work before required architecture gates pass.
- Documentation-first for policy changes; implementation follows approved docs.
- Backward compatibility for public APIs unless an approved migration plan exists.
- PostgreSQL is the single source of truth for runtime and tests.
- Every sprint must declare scope, out-of-scope, and exit criteria.

## Prohibited
- Unplanned scope expansion during a sprint.
- Hidden framework coupling that bypasses defined module boundaries.
- Direct production data mutations without migration scripts and rollback plan.

## Compliance
- PR template must reference relevant standards documents.
- CI gates block merges on standards violations.
