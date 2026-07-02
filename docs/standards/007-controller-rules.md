# 007 Controller Rules

## Responsibilities
- Parse input.
- Delegate to service layer.
- Return standardized responses.

## Prohibited
- Direct repository calls.
- Business rules or transaction logic in controllers.
- Hidden side effects in response formatting.

## Web vs API
- Web controllers render presentation only.
- API controllers return structured API responses only.
- Shared logic must be in services, not duplicated across controllers.
