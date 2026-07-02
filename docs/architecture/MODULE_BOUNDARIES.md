# Module Boundaries

## Core Ownership
- `Core`: kernel lifecycle, module loading, boot orchestration, container, config contracts.
- `Platform` (`app/Platform`): audit, activity, notifications, versioning, AI context, shared cross-cutting services.

## Business/Domain Modules (Current + Planned)
- `Organization`: tenant master metadata (temporary aggregate host during transition).
- `Companies`: company-level domain behavior.
- `Branches`: branch-level domain behavior.
- `Departments`: department-level domain behavior.
- `Identity`: digital identity records only (no auth login behavior).
- `Membership`: user-to-tenant membership and scope assignment.
- `Users`: user profile/account module.
- `Settings`, `Notification`, `Workflow`, `Automation`, `Plugin`: domain-specific capabilities.

## Boundary Rules
- A module owns its own domain model and application services.
- Cross-module calls must use contracts/events, not deep direct repository access.
- Shared cross-cutting concerns must go through Platform services/contracts.
- Controllers in one module cannot call repositories from another module.

## Transition Note
- Organization currently contains multiple sub-domains as legacy scaffold; split remains planned under governance.

