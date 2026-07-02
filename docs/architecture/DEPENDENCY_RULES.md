# Dependency Rules

## Allowed Dependency Direction
1. `Presentation/API` -> `Application`
2. `Application` -> `Domain` (contracts/entities/value objects/events)
3. `Infrastructure` -> `Domain` + `Application` contracts
4. Modules -> `Core` contracts
5. Modules -> `Platform` contracts/services for cross-cutting capabilities

## Forbidden Dependencies
- `Domain` -> `Infrastructure`
- `Domain` -> HTTP, Router, View, Container facades
- `Application` -> concrete UI classes
- `Controller` -> Repository implementations
- Any module -> internals of another module bypassing contract/event boundary

## Boot Dependency Policy
- Module start order is resolved by:
  1. Topological sort on `dependencies`
  2. Priority
  3. Alphabetical name

## Validation
- `module.json` must declare all required fields and explicit dependencies.
- Missing/invalid dependencies are build-time failures.

