# AxiomOS Execution Lock

**Status:** ACTIVE — overrides infrastructure-first habits  
**Effective:** 2026-07-03  
**Audience:** Human team + Cursor AI

---

## Decision

Stop being **Framework Engineers**. Start being **Product Engineers**.

The platform is **frozen**. It exists to support business modules — not to consume sprints.

If we keep investing in Platform, QA frameworks, documentation, SDK, generators, and quality gates **during** development, we will still be building Login + scaffold CRUD six months from now.

---

## Platform = FROZEN

### Do NOT

- Improve Platform core
- Improve ADT / scaffolding generators
- Improve SDK (Plugin, Theme, AI)
- Improve Quality Gates infrastructure
- Improve test framework infrastructure
- Improve architecture / write new architecture reports
- Improve documentation sprints
- Improve release reports / governance
- Improve build system / CI for its own sake

### ONLY FIX

| Category | Examples |
|----------|----------|
| Fatal errors | Boot failure, white screen |
| Runtime | Undefined variables, uncaught exceptions |
| Data | SQL errors, migration failures |
| Routing | Broken API routes, 404 on required endpoints |
| Security | Auth bypass, injection, exposure of secrets |

---

## Testing Policy

### During development (every day)

```bash
php -l <changed-php-files>
composer test:module <ModuleName>    # targeted only
npm run lint && npm run typecheck    # when React changes
```

### Do NOT run during sprints

- `composer quality:gate`
- `composer runtime:test`
- `composer browser:test`
- `composer production:test`

### At module completion (once per module)

1. Run **Playwright** (React) or **Dusk** (API flows): login, navigate, CRUD, forms, reports, dashboard
2. Run **`composer quality:gate`**
3. Fix blockers
4. **Freeze module**
5. Move to next module in build order

**Not** after every CRUD page.

---

## Delivery Rule

**One Run = One COMPLETE business module** — not one screen.

Example — Sales (6.B) in a single delivery:

Quotation · Sales Order · Delivery · Invoice · Customer Portal · Price Lists · Discounts · Taxes · Reports · Dashboard · REST APIs · React UI · Tests · **Freeze**

---

## Build Order (Locked)

```
Phase 6 — Core ERP Business Modules
├── 6.A  Accounting
├── 6.B  Sales
├── 6.C  Purchase
├── 6.D  Inventory
├── 6.E  CRM
├── 6.F  HR
├── 6.G  Payroll
├── 6.H  Manufacturing
├── 6.I  POS
├── 6.J  Fixed Assets
├── 6.K  Projects
└── 6.L  Reporting & BI

Phase 7 — React Enterprise Frontend (production UX polish)

Phase 8 — Electron Desktop + Cloud SaaS + Marketplace

Release v1.0
```

---

## UI Policy

| Layer | Role |
|-------|------|
| **React** (`axiomos-web/`) | Primary ERP UI — all new features |
| **REST API** | Business logic + data |
| **Blade** | Legacy / admin debug only — **no new product screens** |

Flow: **Backend API first → React immediately after.**

---

## React Architecture (Required)

- Feature modules (plugin contract, AMS v2)
- Lazy loading / code splitting
- Plugin loader + dynamic menus
- Theme engine
- Permission-based routing
- Real-time notifications (when API ready)
- PWA / offline-ready shell
- Electron desktop (Phase 8)

---

## Vision

Not just ERP — a category challenger:

| Target | Aim |
|--------|-----|
| Odoo | Better UX |
| ERPNext | Better architecture |
| SAP | Accounting depth |
| Oracle | Security |
| Dynamics | Integrations (post-core) |
| Stack | React + PWA + Electron + Cloud + Marketplace + AI |

**95% business modules · 5% critical bugs**

---

## Cursor Operating Prompt

```
Platform is FROZEN.
Ship complete business modules only (6.A → 6.L).
REST API + React. No Blade-first. No mid-sprint quality:gate.
Full gate + Playwright only when a module is COMPLETE.
Ask: Would Odoo's PM ship this?
```

---

## References

- Product roadmap: `docs/V2_MASTER_ROADMAP.md`
- Product architecture: `docs/architecture/PRODUCT_ARCHITECTURE_LOCK.md`
- Accounting engine: `docs/architecture/ACCOUNTING_BLUEPRINT_LOCK.md`
- Historical platform roadmap: `docs/MASTER_ROADMAP.md` (frozen)
