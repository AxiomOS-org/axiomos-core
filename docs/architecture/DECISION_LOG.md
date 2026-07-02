# Architecture Decision Log

| ID | Date | Decision | Reason | Alternatives | Impact | Status |
|---|---|---|---|---|---|---|
| ADR-001 | 2026-07-02 | Move platform services from `modules/Universal` to `app/Platform` | Platform capabilities are cross-cutting, not business modules | Keep `Universal` as module | Cleaner boundaries, reusable services | Accepted |
| ADR-002 | 2026-07-02 | Boot order must use dependency-aware sorting | Priority-only ordering can violate dependency graph | Priority only | Stable startup and deterministic dependency resolution | Accepted |
| ADR-003 | 2026-07-02 | Enforce `minimumCoreVersion` in module manifests | Core compatibility must be explicit | Optional compatibility field | Safer module upgrades and governance | Accepted |
| ADR-004 | 2026-07-02 | Eloquent repositories belong to `Infrastructure` | Domain purity and hexagonal compliance | Keep Eloquent in Domain | Better testability and layer isolation | Accepted |
| ADR-005 | 2026-07-02 | Production auto-demo seeding disabled | Prevent accidental production seed pollution | Keep seed-on-empty globally | Safer production runtime | Accepted |
| ADR-006 | 2026-07-02 | Standard test pyramid and quality gates required | Full-suite-only strategy does not scale | Ad-hoc testing | Predictable feedback and CI governance | Accepted |
| ADR-007 | 2026-07-02 | Architecture freeze before Identity/Membership | Avoid repeated foundational refactors | Start business modules immediately | Reduced long-term rework | Accepted |
| ADR-008 | 2026-07-02 | Adopt AxiomOS Manifest Specification (AMS) as universal package contract | Installer, marketplace, ADT, upgrade engine, and orchestrator need one schema | Per-package custom metadata | Unified extensibility and lifecycle governance | Accepted |
| ADR-009 | 2026-07-02 | Lock AI Orchestrator architecture with strict Developer AI vs Business AI separation | Prevent unsafe context mixing and governance bypass | Single shared AI runtime | Safer autonomous engineering and ERP AI features | Accepted |
| ADR-010 | 2026-07-02 | Enforce simulation-first generator pipeline (preview + approval before writes) | Prevent accidental destructive generation | Direct write generators | Predictable, auditable ADT operations | Accepted |

