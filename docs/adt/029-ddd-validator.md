# 029 DDD Validator

## Purpose
Validate generated and proposed artifacts against DDD standards.

## Rules
- Domain layer contains only approved DDD constructs.
- Application layer orchestrates use cases, not persistence internals.
- Infrastructure implementations are adapter-only.

## Output
- DDD compliance score
- Violations by layer
- Refactoring recommendations
