# 014 PostgreSQL Rules

## Baseline
- PostgreSQL-only runtime and tests.
- UUID primary keys for platform/module entities.
- JSONB for flexible metadata with explicit indexing strategy.

## Performance Rules
- Define indexes for every frequent filter/sort path.
- Prefer partial indexes for sparse predicates.
- Use `search_vector` + GIN for full-text search where applicable.

## Isolation Rules
- Test suites must use schema isolation (`DB_SCHEMA`/`TEST_SCHEMA`).
- Parallel testing must be configurable and disabled by default until infra is certified.
