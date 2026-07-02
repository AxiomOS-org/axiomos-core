# 002 Folder Structure

## Global Structure
- `app/` for kernel, platform, and shared infrastructure.
- `modules/` for bounded business modules only.
- `docs/` for standards, architecture manual, and ADRs.
- `tests/` for cross-module testing suites.

## Module Structure (Required)
- `Database/`
- `Domain/`
- `Application/`
- `Infrastructure/`
- `Presentation/`
- `API/`
- `Tests/`
- `DemoData/`

## Rules
- Do not place persistence adapters inside `Domain/`.
- Do not place business rules inside `Presentation/` or `API/`.
- Every new directory must map to a clear responsibility.
