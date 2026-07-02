# AxiomOS Manifest Specification (AMS)

## Version
AMS-1.0

## Purpose
Single manifest contract for modules, plugins, themes, AI agents, workflows, automation packages, and marketplace packages.

## Canonical Schema
```json
{
  "name": "Accounting",
  "type": "module",
  "version": "1.0.0",
  "minimumCoreVersion": "1.0.0",
  "dependencies": [],
  "permissions": [],
  "events": [],
  "routes": [],
  "providers": [],
  "migrations": [],
  "seeders": [],
  "ai": {},
  "marketplace": {},
  "upgrade": {}
}
```

## Field Rules
- `name`: unique, canonical module/package name
- `type`: `module | plugin | theme | ai-agent | workflow | automation | marketplace-package`
- `version`: semver
- `minimumCoreVersion`: semver compatibility with core
- `dependencies`: list of required package/module names
- `permissions`: RBAC permission keys exposed
- `events`: published/subscribed event contracts
- `routes`: API/web route declarations (metadata only in manifest)
- `providers`: service provider class names
- `migrations`: migration identifiers/paths
- `seeders`: seeder identifiers/paths
- `ai`: AI capability metadata (planner hooks, context keys)
- `marketplace`: distribution metadata (publisher, signature, category)
- `upgrade`: upgrade strategy metadata (migrations, transformers)

## Consumers
- Installer
- Marketplace
- ADT generators
- Upgrade engine
- AI orchestrator

## Governance
- Manifest changes follow semver and ADR policy for breaking contract changes.
