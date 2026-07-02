# Extension Points

## Official Extension Surfaces

### Plugins
- Module provider registration lifecycle
- Module manifest metadata (`module.json`)

### Marketplace
- Module package distribution and compatibility metadata (`minimumCoreVersion`)

### AI
- AI context service contracts
- Prompt policy and redaction pipeline hooks

### Events
- Domain/integration event emission and subscription points
- Event bus listeners with idempotent handlers

### Automation
- Workflow triggers and actions through declared automation contracts

## Governance
- Extension points are public contracts; breaking changes require semver major or compatibility bridge.
- Undocumented extension points are considered internal and unstable.

