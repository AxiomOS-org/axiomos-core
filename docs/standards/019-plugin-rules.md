# 019 Plugin Rules

## Plugin Governance
- Plugins are optional extensions, not core dependencies.
- Plugin APIs must be versioned and documented.

## Security
- Plugin capabilities require explicit permission boundaries.
- Untrusted plugin code must not access privileged internals directly.

## Lifecycle
- Install, upgrade, and rollback paths must be defined.
- Plugin failures must degrade gracefully without bringing down kernel boot.
