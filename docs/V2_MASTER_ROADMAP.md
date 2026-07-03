# AxiomOS v2 — Product Execution Roadmap

**Status:** Active — business modules only; platform frozen  
**Mode:** Product-first. Complete modules per phase (6.A–6.L).  
**Execution authority:** `docs/EXECUTION_LOCK.md` (testing, freeze, build order)  
**Horizon:** Core ERP complete → Phase 7 UX → Phase 8 Desktop/Cloud/Marketplace → v1.0  
**Last Updated:** 2026-07-03

---

## The Pivot (Why v2 Exists)

AxiomOS was built as a **platform engineering exercise**. The backend is real. The product is not.

| What exists | Reality |
|-------------|---------|
| Laravel modules (30+) | Scaffolded — generic CRUD, not market-ready workflows |
| Blade UI (65 templates) | Bootstrap card shells — not Odoo/Linear/Notion feel |
| React / TypeScript | **Zero files. No package.json. Does not exist.** |
| Accounting backend | **Real** — PostingEngine, reports, journal pipeline |
| Accounting UI | Placeholder dashboard + list pages |
| ERP modules (Sales, Purchase, etc.) | Routes + empty CRUD — no business depth |
| Platform SDKs (ADT, Plugin, Theme, AI) | Premature — defer to v3+ |
| Quality gates | Valuable for backend — but validated shells, not a product |

**Strategic mistake:** UI in Blade when the vision was always React SPA.

**Strategic mistake:** Months on ADT/SDK/Marketplace before Accounting was demoable.

**Decision:** Stop all new Blade. Laravel becomes API-only. React becomes the product.

---

## Vision Lock

```
AxiomOS ≠ Laravel Project

AxiomOS = Modern Enterprise Platform
  ├── React Frontend      (the product users see)
  ├── Laravel REST API    (business logic + data)
  ├── PostgreSQL          (production DB)
  ├── Plugin Marketplace  (v3 — after core ERP ships)
  └── AI Layer            (v3 — after core ERP ships)
```

**Target feel:** Odoo (ERP depth) + Linear (speed/polish) + Notion (clarity) + Stripe (dashboard quality)

**Architecture authority:** `docs/architecture/PRODUCT_ARCHITECTURE_LOCK.md` (PA-1.0)

### Three Deployment Modes (one backend)

| Mode | Packaging |
|------|-----------|
| Cloud SaaS | Nginx + PHP-FPM + PostgreSQL + React static |
| Self-hosted | Same stack, customer-owned server |
| Desktop `.exe` | Tauri + bundled Laravel + PostgreSQL (Odoo-style installer) |

Laravel never becomes an `.exe`. Desktop = React shell → `localhost` API → Laravel → PostgreSQL.

### Plugin Model (locked now, Marketplace in v3)

Every module = **Backend Package + Frontend Package + AMS v2 manifest**.

Core modules (Accounting, Sales) use the **same plugin contract** as Marketplace plugins — no special cases, no rewrite later.

```
Plugin install → manifest read → backend migrate/register → frontend dynamic import
              → sidebar + routes + widgets + permissions auto-register
```

See `PRODUCT_ARCHITECTURE_LOCK.md` for manifest schema, `PluginManager` contract, and install pipeline.

---

## What We Keep (Do Not Rebuild)

These are **done enough** to serve as API layer. No re-architecture.

- Identity, Authentication, Authorization (RBAC, policies, sessions)
- Organization (org / company / branch / department)
- Accounting domain layer (PostingEngine, LedgerEngine, reports services)
- Module loader, DI container, migration runner
- Database schema for Accounting + scaffolded ERP tables
- Security hardening (MFA, audit, rate limiting)

**Rule:** Keep backend services. Expose via REST. Do not touch Blade.

---

## What We Freeze (No New Work)

| Item | Action |
|------|--------|
| Blade views & Web controllers | **FREEZE** — no new pages, no styling investment |
| ADT Generator / certification sprints | **FREEZE** — use only if scaffolding saves time |
| Plugin SDK, Theme SDK, AI SDK | **DEFER → v3** |
| Marketplace, Workflow Builder, Automation Builder | **DEFER → v3** |
| Phase 5D Platform Services (mail, SMS, WhatsApp) | **DEFER** — stub in React when needed |
| Phase 5E AI Platform | **DEFER → v3** |
| New architecture documentation | **STOP** — ADRs only for breaking changes |
| `composer quality:gate` during active UI sprints | **STOP** — use frontend test pyramid instead |

---

## What We Abandon

| Item | Reason |
|------|--------|
| Blade as product UI | Dead end — every hour here is thrown away |
| Browser demo via Blade pages | Replace with React dev server + API |
| Scaffold CRUD as "module complete" | CRUD list pages ≠ ERP module |
| Infrastructure sprint sequencing | Platform is frozen enough |

Blade routes stay alive temporarily for backward compat. They are **not** the demo surface.

---

## Tech Stack (Locked)

### Backend (unchanged)
- Laravel-style PHP modules (existing `axiomos-core`)
- REST JSON APIs (`/api/{module}/...`)
- PostgreSQL
- Existing PostingEngine contract (ACC-BP-1.0)

### Frontend (new — `axiomos-web/`)
| Layer | Choice | Why |
|-------|--------|-----|
| Framework | React 19 + TypeScript | SPA, ecosystem, hiring |
| Build | Vite | Fast HMR, standard |
| Routing | React Router v7 | SPA navigation |
| Data | TanStack Query v5 | Cache, mutations, loading states |
| UI | **shadcn/ui + Tailwind** | Linear/Notion feel; full theme control |
| Forms | React Hook Form + Zod | ERP forms are complex |
| State | Zustand | Auth, company context, theme, sidebar |
| Charts | ApexCharts | Dashboard widgets |
| Tables | AG Grid | Enterprise data grids |
| Motion | Framer Motion | Page transitions, micro-interactions |
| Flows | React Flow | Workflow builder (v3) |
| Editor | Monaco | Code/config editors (v3) |
| PWA | Vite PWA plugin | Offline shell (Run 1 exit) |

MUI is fallback only if shadcn blocks a deadline.

### Repo Layout
```
AxiomOS/
├── axiomos-core/       # Laravel API (existing)
├── axiomos-web/        # React SPA + PluginManager (new)
└── axiomos-desktop/    # Tauri installer (v3 — after web ERP stable)
```

Dev: API on `:8000`, Vite on `:5173`, proxy `/api` → backend.  
Desktop (later): same React build inside Tauri → `http://127.0.0.1:{port}/api`.

---

## Execution Runs (Product Order)

> **LOCKED:** See `docs/EXECUTION_LOCK.md` for platform freeze, testing policy, and delivery rules.  
> One phase = one **complete** business module. Full `quality:gate` + Playwright **once** at module freeze.

### Phase 6 — Core ERP (Business Modules)

| Phase | Module | Complete when |
|-------|--------|---------------|
| **6.A** | Accounting | Full cycle: accounts → journal → post → TB → P&L → periods → audit |
| **6.B** | Sales | Quote → order → delivery → invoice → taxes → dashboard → React + API |
| **6.C** | Purchase | Vendor → PO → GRN → bill → payment → posting |
| **6.D** | Inventory | Warehouses, stock, transfers, valuation → GL |
| **6.E** | CRM | Leads, kanban pipeline, activities |
| **6.F** | HR | Employees, attendance, leave |
| **6.G** | Payroll | Payroll runs → PostingEngine |
| **6.H** | Manufacturing | BOM, work orders, production → GL |
| **6.I** | POS | Touch register, sessions, sale posting |
| **6.J** | Fixed Assets | Register, depreciation runs |
| **6.K** | Projects | Projects, tasks, timesheets |
| **6.L** | Reporting & BI | Dashboards, snapshots, accounting-linked reports |

### Phase 7 — React Enterprise Frontend

Production-quality UX polish across all shipped modules.

### Phase 8 — Distribution

Electron Desktop · Cloud SaaS · Marketplace · **Release v1.0**

---

### Run 1 — React Admin Shell ✅ (Complete)
**Goal:** The product *looks* like AxiomOS for the first time.

| Deliverable | Detail |
|-------------|--------|
| App shell | Sidebar, top nav, breadcrumbs, page layout |
| **PluginManager** | Skeleton registry — `GET /api/platform/plugins` + dynamic route/menu slots |
| **@axiomos/plugin-sdk** | `PluginContext` + `register(ctx)` contract (PA-1.0) |
| Auth flow | Login, session, logout (hits `/api/auth/*`) |
| Company switcher | Multi-company context in Zustand + header dropdown |
| Profile menu | User avatar, settings link, logout |
| Notification center | Drawer UI (mock data → real API in Run 2) |
| Theme | Dark mode + light mode toggle (persisted) |
| Dashboard | Widget grid with ApexCharts placeholders + widget slots |
| Responsive | Mobile sidebar collapse, tablet layout |
| Animations | Framer Motion route transitions |
| PWA | Installable shell, offline login page |

**Exit:** Record a 2-min Loom of the shell. No Blade involved.

**Do NOT:** Build any ERP module screens yet. **Do:** Ship PluginManager skeleton so Run 2 Accounting registers as a plugin, not a hardcoded route.

---

### Run 2 — Accounting (3–4 weeks)
**Goal:** First market-ready module. Challenge Odoo Accounting on core flows.

| Screen | API (exists / build) |
|--------|---------------------|
| Chart of Accounts (tree) | `GET/POST /api/accounting/accounts` ✅ |
| Fiscal Years & Periods | `GET /api/accounting/fiscal-years` ✅ |
| Journal Entry form | `POST /api/accounting/posting/submit` ✅ |
| Posting preview | `POST /api/accounting/posting/preview` ✅ |
| Reversal | `POST /api/accounting/posting/reverse` ✅ |
| Documents list | `GET /api/accounting/documents` ✅ |
| Trial Balance | `GET /api/accounting/reports/trial-balance` ✅ |
| Balance Sheet | `GET /api/accounting/reports/balance-sheet` ✅ |
| P&L | `GET /api/accounting/reports/profit-loss` ✅ |
| Cash Flow | `GET /api/accounting/reports/cash-flow` ✅ |
| Voucher Types | `GET /api/accounting/voucher-types` ✅ |
| Cost Centers | `GET /api/accounting/dimensions/cost-centers` ✅ |

**UI depth required:**
- AG Grid for account lists, journal lines, trial balance
- Tree view for chart of accounts
- Multi-line journal entry form with debit/credit validation
- Report pages with date range picker + export CSV
- Posting status indicators, period lock warnings

**Exit:** Complete accounting cycle demo: create accounts → journal entry → post → trial balance → P&L.

**Backend rule (LOCKED):** All financial writes through PostingEngine. No exceptions.

---

### Run 3 — Sales (2–3 weeks)
Quotation → Sales Order → Delivery → Invoice → Payment → **PostingEngine**

Focus: Document workflow, not CRUD lists. Status transitions. Print/PDF.

---

### Run 4 — Purchase (2 weeks)
Vendor → RFQ → PO → GRN → Bill → Payment → **PostingEngine**

Mirror Sales patterns. Reuse document components.

---

### Run 5 — Inventory (2–3 weeks)
Warehouses, stock levels, transfers, batch/serial. Stock ledger → **PostingEngine**

---

### Run 6 — CRM (2 weeks)
Leads, pipeline (kanban), activities. No financial posting.

---

### Run 7 — HR (2–3 weeks)
Employees, attendance, leave. Payroll → **PostingEngine**

---

### Run 8 — POS (2 weeks)
Touch-friendly UI. Shift, cash drawer. **PostingEngine** on sale.

---

### Run 9 — Manufacturing (3 weeks)
BOM, work orders, MRP basics. **PostingEngine** on production.

---

### Deferred (v3+)
| Phase | When |
|-------|------|
| Projects, Fixed Assets, Budgeting, Reporting BI | After Run 9 |
| Plugin Marketplace | After 3+ modules are market-ready |
| Workflow / Automation Builder | After Marketplace foundation |
| AI Assistant / AI Studio | After Marketplace |
| Mobile apps (React Native) | After web ERP stable |
| Multi-tenant SaaS / Billing | After product-market fit |

---

## Odoo Challenge Map (Accounting First)

| Odoo Feature | AxiomOS Run 2 Target | Priority |
|--------------|---------------------|----------|
| Chart of Accounts | Tree + drag reorder | P0 |
| Journal Entries | Multi-line form + preview | P0 |
| Reconciliation | Bank rec UI | P1 (post-Run 2) |
| Financial Reports | TB, BS, P&L, Cash Flow | P0 |
| Multi-currency | Exchange rates + conversion | P1 |
| Fiscal periods | Open/close period | P0 |
| Analytic accounts | Cost/profit centers | P1 |
| Audit trail | Posting log viewer | P0 |
| Budget vs Actual | Budgeting module | v3 |

---

## API Strategy

1. **Existing Accounting APIs** — consume directly from React.
2. **New endpoints** — add to `*ApiController` only when a screen needs them.
3. **No new Web controllers** — Blade freeze.
4. **API contract** — JSON `{ data, meta, errors }` consistent shape.
5. **Auth** — session cookie or Bearer token; React uses `credentials: 'include'`.
6. **CORS** — configure for `:5173` in dev.

---

## Quality Gates (v2)

| When | Run |
|------|-----|
| Every commit | `npm run lint && npm run typecheck` |
| Daily | `npm run test` (Vitest) |
| Run complete | `composer test:module Accounting` + manual demo recording |
| Phase release | `composer quality:gate` on backend only |

Frontend does not need PHP browser tests. Replace with Playwright smoke tests in Run 1.

---

## Week 1 Immediate Actions

| Day | Task |
|-----|------|
| 1 | Scaffold `axiomos-web/` (Vite + React + TS + Tailwind + shadcn) |
| 1 | Configure API proxy → `localhost:8000` |
| 2 | App shell: sidebar + top nav + layout |
| 2 | React Router routes stub |
| 3 | Auth pages → wire to existing auth API |
| 3 | Zustand store: user, company, theme |
| 4 | Dark/light mode + Framer Motion layout |
| 4 | Company switcher component |
| 5 | Dashboard widget grid + ApexCharts sample |
| 5 | Notification drawer (mock) + profile menu |

**Stop:** Any Blade styling. Any new SDK work. Any documentation sprints.

---

## Cursor / AI Operating Rules (v2)

```
You are the Product Engineering Team.

Behave like a product team shipping a React ERP.
NOT like an architect writing platform specs.

Every sprint must produce something a user can click.
If it can't be demoed in a browser at localhost:5173, it doesn't count.

Ask: "Would Odoo's PM ship this?" If no, don't ship it.
```

---

## Success Metrics (3-Month)

| Metric | Target |
|--------|--------|
| React admin shell | Demoable |
| Accounting module | Full cycle works end-to-end |
| Sales + Purchase | Invoice → posting works |
| Blade new pages | 0 |
| SDK sprints | 0 |
| Paying pilot user | 1 (stretch) |

---

## Reference (Frozen — Do Not Re-debate)

- Accounting PostingEngine: `docs/architecture/ACCOUNTING_BLUEPRINT_LOCK.md`
- Platform architecture: `docs/architecture/ARCHITECTURE_FREEZE.md`
- v1 roadmap (historical): `docs/MASTER_ROADMAP.md`

This document is the **active execution authority** for all product work.
