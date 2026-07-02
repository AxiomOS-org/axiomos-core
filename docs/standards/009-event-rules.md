# 009 Event Rules

## Event Model
- Domain events represent business facts in past tense.
- Integration events are explicit contracts between modules.

## Rules
- Emit events from application/domain boundaries, not controllers.
- Event handlers must be idempotent.
- Use async processing for non-critical side effects.
- Keep payloads minimal and versioned.

## Governance
- Document every event in module docs.
- Breaking event changes require migration/compatibility plan.
