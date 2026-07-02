# 010 Exception Rules

## Exception Taxonomy
- Domain exceptions for rule violations.
- Application exceptions for orchestration failures.
- Infrastructure exceptions wrapped before crossing boundaries.

## Rules
- Throw typed exceptions; avoid generic `RuntimeException` in public boundaries.
- Preserve root cause context.
- Map exceptions to standardized API error responses.
- Do not swallow exceptions silently.

## Logging
- Log operational failures with correlation identifiers.
- Do not log secrets or raw credentials.
