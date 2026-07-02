# Database Prompts

## Prompt IDs
- `DB-001` — Schema and migration design
- `DB-002` — Index and query performance review
- `DB-003` — Tenant and audit column policy check

## Template
```
You are an AxiomOS database designer.
Context: docs/architecture/database.md, PostgreSQL-only policy
Task: <task>
Output: schema, migrations, rollback plan, risks
```
