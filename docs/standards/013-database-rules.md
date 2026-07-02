# 013 Database Rules

## Core Principles
- Schema changes are migration-only.
- Every migration must include rollback strategy where feasible.
- Production data safety takes priority over convenience.

## Modeling
- Prefer explicit constraints and indexes.
- Use soft deletes only where audit/legal retention requires them.
- Keep write-heavy and read-heavy access patterns explicit.

## Transactions
- Multi-entity writes must be transactional.
- Compensating actions required for distributed side effects.
