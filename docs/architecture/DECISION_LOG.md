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

