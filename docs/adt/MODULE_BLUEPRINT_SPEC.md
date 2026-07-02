# Module Blueprint Specification

## Purpose
`axiomos:make-module` creates a full engineering blueprint, not only scaffold files.

## Required Root Files
- `module.json` (AMS-compliant)
- `README.md`
- `ARCHITECTURE.md`
- `CHANGELOG.md`
- `TESTING.md`

## Recommended Root Files
- `ROADMAP.md`
- `TODO.md`

## Required Directories
- `Database/` (Migrations, Seeders, Factories)
- `Domain/` (Models, Repositories/Contracts, Events, Enums, ValueObjects, Policies)
- `Application/` (Services, DTOs, Support, Commands, Listeners)
- `Infrastructure/` (Persistence, External)
- `Presentation/` (Views, Components, Layouts)
- `API/` (Controllers, Resources, Requests)
- `Tests/` (Unit, Feature, Integration)
- `DemoData/`
- `Providers/`

## Simulation Report (before write)
The command must print:
- Module name
- Directory count
- File count
- Routes, entities, controllers (0 for blueprint phase)
- Estimated time
- Conflicts
- Dependencies
- Developer approval prompt (`Y/N`)

## Write Policy
Files are written only after explicit developer approval.
