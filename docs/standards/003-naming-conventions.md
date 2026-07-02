# 003 Naming Conventions

## General
- Use explicit, domain-driven names.
- Use singular class names (`OrganizationService`), plural collection concepts in docs only.
- Use ASCII identifiers; avoid ambiguous abbreviations.

## Technical
- Interfaces end with `Interface`.
- Repositories named `<Entity>RepositoryInterface` and `Eloquent<Entity>Repository`.
- Events use past tense (`OrganizationCreated`).
- DTOs use suffix `DTO`.

## Modules
- Canonical names only: `Organization`, `Companies`, `Branches`, `Departments`, `Users`, `Identity`, `Membership`, `Settings`, `Notification`, `Workflow`, `Automation`, `Plugin`.
- No duplicate singular/plural scaffold modules.
