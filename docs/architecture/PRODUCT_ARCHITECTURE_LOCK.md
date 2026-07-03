# AxiomOS Product Architecture Lock

**Version:** PA-1.0  
**Status:** LOCKED — governs all frontend, deployment, and plugin work  
**Supersedes:** Blade UI strategy, backend-only `module.json` (extended, not replaced)  
**Last Updated:** 2026-07-03

---

## Decision Summary

AxiomOS is **one backend, one frontend contract, three deployment modes**.

```
                    ┌─────────────────────────────────────┐
                    │         AxiomOS Core Backend        │
                    │   Laravel API + PostgreSQL          │
                    │   ModuleLoader + Plugin Lifecycle   │
                    └──────────────┬──────────────────────┘
                                   │ REST API
          ┌────────────────────────┼────────────────────────┐
          │                        │                        │
   ┌──────▼──────┐         ┌───────▼───────┐        ┌───────▼───────┐
   │  Web SPA    │         │  Cloud SaaS   │        │   Desktop     │
   │  (Browser)  │         │  (Multi-tenant)│       │  (.exe/.dmg)  │
   │  React      │         │  React + API  │        │  Tauri/Electron│
   └─────────────┘         └───────────────┘        └───────────────┘
```

**Rule:** Deployment changes packaging only. Business logic, APIs, and plugin contracts never fork.

---

## Three Deployment Modes

| Mode | Target | Stack | Notes |
|------|--------|-------|-------|
| **Cloud SaaS** | AWS, Azure, DO, Hetzner, Hostinger VPS | Nginx + PHP-FPM + PostgreSQL + React static build | Multi-tenant later (Phase 7E) |
| **Self-hosted** | Linux / Windows Server | Same as Cloud — customer owns the server | Single-tenant default |
| **Desktop Installer** | Windows `.exe` (macOS `.dmg`, Linux `.AppImage` later) | Tauri shell + embedded/local backend + PostgreSQL | Odoo/SQL Server installer UX |

All three require **PostgreSQL only** in production. SQLite is dev/test only.

---

## Desktop Architecture (Not "Laravel as .exe")

Laravel does not compile to an executable. The correct model:

```
┌─────────────────────────────────────────────────────────┐
│  AxiomOS Desktop (Tauri)                                │
│  ┌───────────────────────────────────────────────────┐  │
│  │  React Shell (same axiomos-web build)           │  │
│  │  PluginManager · Sidebar · Auth · Theme           │  │
│  └───────────────────────┬───────────────────────────┘  │
│                          │ http://127.0.0.1:{port}/api  │
│  ┌───────────────────────▼───────────────────────────┐  │
│  │  Laravel Backend (bundled PHP runtime)            │  │
│  │  Windows Service / systemd user unit              │  │
│  └───────────────────────┬───────────────────────────┘  │
│  ┌───────────────────────▼───────────────────────────┐  │
│  │  PostgreSQL (bundled OR connect external)           │  │
│  └───────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

### Installer Flow (`AxiomOS-Setup.exe`)

```
Next → Next → Finish

1. Extract PHP runtime + Laravel backend
2. Install/start PostgreSQL (bundled portable OR detect existing)
3. Run migrations + seed core plugins
4. Install React desktop shell (Tauri)
5. Register Windows Service (backend auto-start)
6. Launch AxiomOS
```

User never sees PHP, Laravel, or PostgreSQL. Same mental model as Odoo or SQL Server Express.

### Desktop vs Web

| Concern | Web | Desktop |
|---------|-----|---------|
| React build | Identical | Identical |
| API base URL | `/api` or env `VITE_API_URL` | `http://127.0.0.1:{auto-port}/api` |
| Plugin bundles | CDN / static host | `%APPDATA%/AxiomOS/plugins/` |
| Auth | Cookie / Bearer | Same — localhost trusted origin |

**Decision:** Tauri primary (small binary, native feel). Electron fallback if Tauri blocks plugin hot-load.

---

## Cloud / Self-hosted Architecture

```
Internet
   │
   ▼
Nginx (TLS termination, static React, /api proxy)
   │
   ├──► /          → React build (axiomos-web/dist)
   │
   └──► /api/*     → PHP-FPM → Laravel (axiomos-core)
                           │
                           ▼
                      PostgreSQL
```

- React build is static files — no Node in production.
- Plugin frontend bundles served from `/plugins/{id}/{version}/` or CDN.
- Same Docker image works on any VPS — only env vars change.

---

## Plugin Architecture (Dual Package)

**Never Blade plugins. Never frontend-less backend modules.**

Every installable unit — core or marketplace — is a **Plugin Package**:

```
Accounting/
├── manifest.json          # Single source of truth (AMS v2)
├── backend/               # PHP module (or backend.zip)
│   ├── Providers/
│   ├── Application/
│   ├── Domain/
│   ├── Infrastructure/
│   ├── Database/
│   ├── routes.php
│   └── ...
├── frontend/              # React module (or frontend.zip)
│   ├── index.ts           # Plugin entry — register() export
│   ├── routes.tsx
│   ├── menu.ts
│   ├── permissions.ts
│   ├── pages/
│   ├── widgets/
│   ├── hooks/
│   ├── api/
│   └── assets/
└── translations/
    ├── en.json
    └── ur.json
```

### Core Principle

> **Built-in modules use the same plugin contract as marketplace modules.**

Accounting is not special-cased in the shell. It registers via `manifest.json` exactly like an HR plugin downloaded from the Marketplace. This prevents a rewrite when Marketplace ships.

---

## Manifest Schema (AMS v2)

Extends existing `module.json`. Backend `ModuleLoader` reads top-level fields; frontend reads `frontend` block.

```json
{
  "name": "Accounting",
  "uuid": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
  "version": "1.0.0",
  "description": "Enterprise accounting — GL, posting engine, financial reports",
  "minimumCoreVersion": "2.0.0",
  "enabled": true,
  "priority": 150,
  "dependencies": ["Organization", "Identity"],
  "authors": [{ "name": "AxiomOS Team" }],

  "backend": {
    "provider": "Modules\\Accounting\\Providers\\AccountingServiceProvider",
    "routes": "routes.php",
    "migrations": true,
    "permissions": "permissions.json",
    "apiPrefix": "/api/accounting"
  },

  "frontend": {
    "entry": "frontend/index.ts",
    "routes": "frontend/routes.tsx",
    "menu": "frontend/menu.ts",
    "permissions": "frontend/permissions.ts",
    "widgets": "frontend/widgets/index.ts",
    "icon": "frontend/assets/icon.svg",
    "basePath": "/accounting"
  }
}
```

### Satellite files (optional, referenced by manifest)

| File | Purpose |
|------|---------|
| `permissions.json` | Backend RBAC permission keys |
| `menu.json` | Static menu fallback (prefer `menu.ts` for i18n) |
| `routes.json` | API route catalog for OpenAPI generation |

### Backward compatibility

Existing `module.json` files (provider at root) remain valid. `ModuleLoader` treats missing `backend` block as:

```json
{ "backend": { "provider": "<root provider field>" } }
```

Migration to AMS v2 is incremental — no big-bang rewrite.

---

## Backend Plugin Lifecycle

Extends existing `App\Core\Module\ModuleLoader` (already validates name, version, deps, provider).

```
Install / Discover
      │
      ▼
Read manifest.json
      │
      ▼
Validate deps + core version
      │
      ▼
Register ServiceProvider
      │
      ▼
Run migrations (if backend.migrations)
      │
      ▼
Register routes (routes.php)
      │
      ▼
Register policies + permissions
      │
      ▼
Emit PluginInstalled event
      │
      ▼
Expose via GET /api/platform/plugins
```

### Platform API (new — build in Run 1)

```
GET  /api/platform/plugins          # All installed plugins (manifest summary)
GET  /api/platform/plugins/{id}       # Full manifest + frontend entry URL
POST /api/platform/plugins/install    # Marketplace install (v3)
POST /api/platform/plugins/{id}/enable
POST /api/platform/plugins/{id}/disable
```

Frontend never hardcodes module list. It asks the API.

---

## Frontend Plugin Registry

```
App
 └── PluginProvider
      └── PluginManager
            │
            ├── fetch /api/platform/plugins
            ├── for each enabled plugin with frontend:
            │     ├── dynamic import(entry URL)
            │     ├── call plugin.register(ctx)
            │     ├── collect routes  → React Router
            │     ├── collect menu      → Sidebar
            │     ├── collect widgets   → Dashboard grid
            │     ├── collect perms     → Auth guard
            │     └── collect api hooks → TanStack Query keys
            └── render <Outlet />
```

### Plugin entry contract (TypeScript)

```typescript
// Every plugin exports this from frontend/index.ts
import type { PluginContext } from '@axiomos/plugin-sdk';

export function register(ctx: PluginContext): void {
  ctx.routes.register(accountingRoutes);
  ctx.menu.register({
    id: 'accounting',
    label: 'Accounting',
    icon: 'ledger',
    path: '/accounting',
    children: [...],
  });
  ctx.widgets.register('dashboard.accounting', AccountingSummaryWidget);
  ctx.permissions.register(['accounting.view', 'accounting.post']);
  ctx.api.registerClient('accounting', accountingApiClient);
}
```

### PluginContext capabilities

| API | Purpose |
|-----|---------|
| `ctx.routes` | React Router route objects |
| `ctx.menu` | Sidebar items (nested) |
| `ctx.widgets` | Dashboard widget slots |
| `ctx.permissions` | Frontend permission keys |
| `ctx.api` | Typed API client registration |
| `ctx.i18n` | Translation namespace |
| `ctx.store` | Optional Zustand slice registration |

**No manual file edits** when a plugin installs. Shell is empty except core chrome.

---

## Marketplace Install Pipeline (v3 — contract locked now)

```
Marketplace
     │
     ▼
Download: backend.zip + frontend.zip + manifest.json
     │
     ▼
Verify signature + dep graph
     │
     ▼
Extract backend → modules/{id}/ or plugins/{uuid}/
Extract frontend → public/plugins/{id}/{version}/ or desktop plugins dir
     │
     ▼
Backend: migrate + register provider
Frontend: PluginManager.refresh()
     │
     ▼
Sidebar updates · Routes live · Widgets appear
     │
     ▼
Done — no app rebuild
```

### Example: HR Plugin from Marketplace

**Backend auto-registers:** migrations, `/api/hr/*`, policies, events  
**Frontend auto-registers:** HR menu, Employee page, Attendance, Payroll, dashboard widgets

Zero changes to core `axiomos-web` source tree.

---

## Repository Layout (Locked)

```
AxiomOS/
├── axiomos-core/                    # Laravel API backend
│   ├── app/Core/Module/             # ModuleLoader (extend for AMS v2)
│   ├── modules/
│   │   ├── Accounting/
│   │   │   ├── manifest.json        # AMS v2 (rename from module.json)
│   │   │   ├── backend/ ...         # OR flat until migrated
│   │   │   └── (frontend lives in axiomos-web for core modules)
│   │   └── ...
│   └── plugins/                     # Marketplace-installed backends (v3)
│
├── axiomos-web/                     # React product shell
│   ├── src/
│   │   ├── app/                     # App shell, layout, auth
│   │   ├── core/                    # PluginManager, PluginProvider
│   │   ├── plugins/                 # Core plugin frontends (dev bundled)
│   │   │   ├── accounting/
│   │   │   ├── sales/
│   │   │   └── ...
│   │   └── sdk/                     # @axiomos/plugin-sdk types
│   └── vite.config.ts               # Dynamic plugin chunk support
│
└── axiomos-desktop/                 # Tauri wrapper (Run 10+)
    ├── src-tauri/
    └── bundles backend + postgres + web dist
```

### Dev vs Production plugin loading

| Environment | Core plugins | Marketplace plugins |
|-------------|--------------|---------------------|
| Dev (Vite) | Bundled from `src/plugins/*` via import | Simulated from `public/plugins/` |
| Production Web | Pre-built chunks in `dist/plugins/` | Downloaded to `plugins/` at install |
| Desktop | Same chunks | `%APPDATA%/AxiomOS/plugins/` |

---

## What NOT To Build

| Anti-pattern | Why |
|--------------|-----|
| Blade plugin pages | Dead — React only |
| Special-case Accounting in shell | Forces Marketplace rewrite |
| Separate desktop codebase | One React build, different wrapper |
| Monolithic frontend routes file | Plugins must self-register |
| Plugin install requiring `npm run build` | Marketplace must be hot-install |
| MySQL / SQLite in production | PostgreSQL only |

---

## Build Sequence (When, Not If)

| Phase | Deliverable | Plugin work |
|-------|-------------|-------------|
| **Run 1** | React shell | `PluginManager` skeleton + `GET /api/platform/plugins` stub |
| **Run 2** | Accounting UI | First full plugin (AMS v2 manifest, register contract) |
| **Run 3–9** | ERP modules | Each module = plugin package (frontend + backend) |
| **v3** | Marketplace | Install pipeline, signing, `backend.zip` + `frontend.zip` |
| **v3** | Desktop `.exe` | Tauri installer bundling same API + React |
| **v3+** | AI Layer | AI plugin type in manifest (`"type": "ai-agent"`) |

**Critical:** Run 1 must include `PluginManager` even if it only loads one hardcoded plugin. The contract is set on day one.

---

## Comparison: AxiomOS vs Odoo

| | Odoo | AxiomOS |
|---|------|---------|
| Module format | Python package | Backend PHP + Frontend React |
| UI | QWeb / OWL (monolith) | React SPA (plugin chunks) |
| Install | Apps menu → download | Marketplace → hot install, no rebuild |
| Desktop | Bundled Python | Tauri + Laravel + PostgreSQL |
| Marketplace | Odoo App Store | backend.zip + frontend.zip + manifest |
| Dev platform | Odoo Studio | ADT (internal, v3+) |

Target: **match Odoo ERP depth, exceed on UI speed and install experience.**

---

## Locked Invariants

1. **One backend** — Laravel API serves Web, Cloud, and Desktop.
2. **One frontend contract** — `register(ctx)` via AMS v2 manifest.
3. **PostgreSQL only** in production.
4. **No Blade** — ever, for product UI.
5. **Core modules = plugins** — same manifest, same lifecycle.
6. **Plugin install never requires frontend rebuild** (production).
7. **PostingEngine** remains the sole financial write path (ACC-BP-1.0).

---

## References

- Accounting domain lock: `ACCOUNTING_BLUEPRINT_LOCK.md`
- Platform kernel freeze: `ARCHITECTURE_FREEZE.md`
- Product execution order: `../V2_MASTER_ROADMAP.md`
- Existing backend loader: `app/Core/Module/ModuleLoader.php`
