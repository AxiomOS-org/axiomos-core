# AxiomOS Platform Freeze

**Effective:** 2026-07-02 (after Phase 5.C.6)  
**Status:** FROZEN

---

## Frozen Layers

The following are **production-frozen**. Changes are limited to **bug fixes**, **security patches**, and **test/QA hardening** — no new features.

| Layer | Scope |
|-------|--------|
| **Core** | `app/Core/` — kernel, container, module loader, HTTP bridge |
| **Platform** | `app/Platform/` — universal services, attachments, audit, workflow hooks |
| **ADT** | `app/ADT/` — module generator, extension registry |
| **Identity** | `modules/Identity/`, `modules/Users/`, `modules/Membership/`, `modules/Organization/` |
| **Security** | `modules/Authentication/`, `modules/Authorization/` |
| **QA** | Stability/runtime/browser/production/architecture/performance/security/reliability/QA test suites and bin gate scripts |

---

## Mandatory Gates (Every Business Module)

Before any module is marked **Production Ready**:

```bash
composer runtime:test
composer stability:test
composer browser:test
composer production:test
composer architecture:test
composer performance:test
composer security:test
composer quality:gate
```

**Any failure = module NOT production ready.**

---

## What Opens Next

**Phase 6 — Business ERP Domains** (in order):

1. **6.A** Accounting Foundation (GL first)
2. **6.B** Sales through **6.L** Reporting & BI (see `MASTER_ROADMAP.md`)

**No new infrastructure or platform features** unless required as a minimal platform bugfix with full QA gate.

---

## Exception Process

1. Document the bug/security issue
2. Minimal diff only
3. Full quality gate must pass
4. Update `docs/reports/` with fix note
