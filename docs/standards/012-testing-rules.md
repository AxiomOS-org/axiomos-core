# 012 Testing Rules

## Test Pyramid (Required)
- Unit
- Module
- Integration
- E2E
- Performance
- Security

## Execution Commands
- `composer test:unit`
- `composer test:module <ModuleName>`
- `composer test:integration`
- `composer test:e2e`
- `composer test:performance`
- `composer test:security`
- `composer test:all`

## Rules
- Prefer focused suite runs during development.
- Full suite runs in CI and before release.
- Tests must be deterministic and isolated.
