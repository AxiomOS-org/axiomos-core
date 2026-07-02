# 006 Repository Rules

## Repository Contracts
- Define repository interfaces in `Domain/Repositories/Contracts`.
- Expose domain-oriented methods, not framework query details.

## Implementations
- Place all Eloquent implementations in `Infrastructure/Persistence`.
- Never place Eloquent repositories in `Domain/`.

## Query Rules
- Use indexed fields for filtering and sorting defaults.
- Keep pagination explicit and consistent.
- Avoid leaking request objects into repository signatures.
