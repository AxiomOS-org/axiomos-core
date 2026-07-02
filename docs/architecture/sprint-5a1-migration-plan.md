# Sprint 5.A.1 Migration Plan (P0)

## Objective
Refactor architecture only. No new product features, UI flows, APIs, or database tables.

## Scope
1. Module layer standardization
2. Repository boundary hardening (Domain -> Infrastructure)
3. Boot dependency ordering hardening
4. Test architecture and quality gates

## Execution Phases

### Phase 1 - Module Layout Normalization
- Ensure every module has:
  - `Database/`
  - `Domain/`
  - `Application/`
  - `Infrastructure/`
  - `Presentation/`
  - `API/`
  - `Tests/`
  - `DemoData/`

### Phase 2 - Repository Refactor
- Move Eloquent repositories from:
  - `modules/Organization/Domain/Repositories/*`
- To:
  - `modules/Organization/Infrastructure/Persistence/*`
- Update DI bindings in `OrganizationServiceProvider`.

### Phase 3 - Module Boot Hardening
- Enforce `module.json` validation fields:
  - `name`
  - `version`
  - `dependencies`
  - `minimumCoreVersion`
  - `provider`
- Resolve boot order with:
  1. Topological dependency order
  2. Priority
  3. Alphabetical name

### Phase 4 - Testing Architecture
- Add dedicated suites:
  - Unit
  - Module
  - Integration
  - E2E
  - Performance
  - Security
- Add commands:
  - `composer test:unit`
  - `composer test:module <ModuleName>`
  - `composer test:integration`
  - `composer test:e2e`
  - `composer test:performance`
  - `composer test:security`
  - `composer test:all`

### Phase 5 - Quality Gates
- Add executable gates:
  - Lint
  - Static analysis (placeholder gate)
  - Architecture rules
  - Unit tests
  - Module tests
  - Integration tests
  - Performance smoke
  - Security suite
- Add CI workflow in `.github/workflows/quality-gates.yml`.

## Rollback Strategy
- Revert the repository namespace move and DI import changes.
- Revert module manifest schema enforcement in `ModuleLoader`.
- Re-run `composer test:all` after rollback.

## Exit Criteria
- Architecture-only changes complete.
- Test architecture commands available and green.
- Quality gate workflow and local gate script pass.
