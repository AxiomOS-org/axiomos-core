# 008 API Rules

## API Contract
- Stable JSON envelope format across modules.
- Explicit pagination metadata for list endpoints.
- Versioning required for breaking changes (`/api/v1` baseline).

## Validation
- Validate all write inputs.
- Reject unknown/unsafe fields.
- Never allow client-controlled audit fields unless explicitly whitelisted.

## Errors
- Use consistent typed error shape.
- Avoid exposing internal stack details.
- Provide actionable, localized-safe messages.
