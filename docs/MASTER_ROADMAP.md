# AxiomOS Enterprise Master Roadmap

**Status:** Active — single source of truth for all development  
**Mode:** Code-first execution (no documentation-only sprints)  
**Cadence:** 2–4 days per complete enterprise module  
**Last Updated:** 2026-07-02

---

## Execution Rule (Every Sprint)

No new 30-page documentation sprints. Each sprint delivers:

1. **Design** — brief scope + exit criteria only
2. **Code**
3. **Tests**
4. **Browser Demo**
5. **APIs**
6. **Documentation** — update existing docs only (README, CHANGELOG, module docs)
7. **Review**
8. **Move Next**

### Locked Platform Rules (do not re-debate)
- Architecture freeze: `docs/architecture/ARCHITECTURE_FREEZE.md`
- ADT Rule 0: simulation → approval → write (`docs/adt/ADT_RULE_ZERO.md`)
- AMS manifest contract: `docs/architecture/AMS_SPECIFICATION.md`
- Quality gates must pass before sprint close

### Role Split
- **Cursor/AI:** implementation following this roadmap
- **Architect (human):** review, ADRs, problem solving, go/no-go

---

## Current Position

| Phase | Status |
|-------|--------|
| 5A Platform | ✅ FROZEN |
| 5B Identity Platform | ✅ COMPLETE |
| 5C Security Platform | ✅ COMPLETE |
| 5D Platform Services | ⏭ NEXT |
| 5E AI Platform | Planned |
| 6+ Business ERP | Planned |

**Next run:** 5.D Shared Platform Services (Notifications, Workflow, Automation, Scheduler, Queue, Mail, SMS, Webhooks)

**Strategic note:** Accounting Core (6.A) should follow Security — before Sales, Inventory, Purchase modules. ERP transactions post to General Ledger.

---

## Phase 5A — Platform Finalization

| ID | Sprint | Deliverable |
|----|--------|-------------|
| 5.A.1 | Architecture Hardening | ✅ |
| 5.A.2 | Engineering Standards | ✅ |
| 5.A.3 | Architecture Freeze | ✅ |
| 5.A.4 | ADT Specification | ✅ |
| 5.A.5 | ADT Generator Engine | `make-module` + simulation pipeline |
| 5.A.6 | ADT Certification | Demo module + quality gate proof |
| 5.A.7 | Marketplace SDK | |
| 5.A.8 | Plugin SDK | |
| 5.A.9 | Theme SDK | |
| 5.A.10 | AI SDK | |
| 5.A.11 | Workflow SDK | |
| 5.A.12 | Automation SDK | |
| 5.A.13 | Integration SDK | |
| 5.A.14 | Release Manager | |
| 5.A.15 | Production Freeze | Platform locked for business layer |

---

## Phase 5B — Identity Platform

| ID | Module |
|----|--------|
| 5.B.1 | Identity |
| 5.B.2 | Users |
| 5.B.3 | Membership |
| 5.B.4 | Organizations |
| 5.B.5 | Companies |
| 5.B.6 | Branches |
| 5.B.7 | Departments |
| 5.B.8 | Teams |
| 5.B.9 | Employee Profiles |
| 5.B.10 | Contacts |
| 5.B.11 | Devices |
| 5.B.12 | Sessions |
| 5.B.13 | Login History |
| 5.B.14 | API Tokens |
| 5.B.15 | Identity Dashboard |

---

## Phase 5C — Security Platform

| ID | Module |
|----|--------|
| 5.C.1 | Authentication |
| 5.C.2 | Authorization |
| 5.C.3 | RBAC |
| 5.C.4 | Permissions |
| 5.C.5 | Policies |
| 5.C.6 | Roles |
| 5.C.7 | MFA |
| 5.C.8 | Password Policies |
| 5.C.9 | Audit Security |
| 5.C.10 | Session Manager |
| 5.C.11 | API Security |
| 5.C.12 | Rate Limiting |
| 5.C.13 | OAuth |
| 5.C.14 | SSO |
| 5.C.15 | Security Center |

---

## Phase 5D — Platform Services

| ID | Module |
|----|--------|
| 5.D.1 | Settings |
| 5.D.2 | Localization |
| 5.D.3 | Currency |
| 5.D.4 | Timezones |
| 5.D.5 | Languages |
| 5.D.6 | Notification Center |
| 5.D.7 | Mail |
| 5.D.8 | SMS |
| 5.D.9 | WhatsApp |
| 5.D.10 | Storage |
| 5.D.11 | Files |
| 5.D.12 | Scheduler |
| 5.D.13 | Queue |
| 5.D.14 | Search |
| 5.D.15 | Dashboard Widgets |

---

## Phase 5E — AI Platform

| ID | Module |
|----|--------|
| 5.E.1 | AI Runtime |
| 5.E.2 | AI Gateway |
| 5.E.3 | AI Memory |
| 5.E.4 | AI Context |
| 5.E.5 | AI Prompt Library |
| 5.E.6 | AI Agents |
| 5.E.7 | Voice Assistant |
| 5.E.8 | Realtime AI |
| 5.E.9 | AI Automation |
| 5.E.10 | AI Analytics |
| 5.E.11 | AI Review |
| 5.E.12 | AI Code Assistant |
| 5.E.13 | AI Business Assistant |
| 5.E.14 | AI Marketplace |
| 5.E.15 | AI Studio |

---

## Phase 6A — Finance Foundation

Chart Of Accounts · Fiscal Years · Financial Periods · Journals · Journal Entries · Ledger · Trial Balance · Balance Sheet · Profit & Loss · Cash Flow · Cost Centers · Budgets · Financial Reports · Closing Process · Finance Dashboard

---

## Phase 6B — Sales

Customer · Quotation · Sales Order · Invoice · Delivery · Returns · Payments · Sales Analytics

---

## Phase 6C — Purchase

Vendor · Purchase RFQ · Purchase Order · GRN · Bills · Vendor Payments

---

## Phase 6D — Inventory

Warehouses · Locations · Stock · Transfers · Batch · Serial · Expiry · Barcode

---

## Phase 6E — Manufacturing

BOM · Production · MRP · Work Orders · Machines · Maintenance · Quality

---

## Phase 6F — HR

Employees · Attendance · Leaves · Payroll · Recruitment · Performance

---

## Phase 6G — CRM

Leads · Pipeline · Meetings · Activities · Campaigns

---

## Phase 6H — Projects

Projects · Tasks · Kanban · Timesheets · Milestones

---

## Phase 6I — POS

POS · Shift · Cash Drawer · Kitchen · Receipt · Loyalty

---

## Phase 7A — Marketplace

Marketplace · Plugin Store · Theme Store · AI Store · License Manager

---

## Phase 7B — Workflow Engine

Workflow Builder · Automation Builder · Business Rules · Approvals · Triggers

---

## Phase 7C — Reports

BI · Charts · Dashboards · KPI · Exports

---

## Phase 7D — Mobile

Android · iOS · Offline Sync · Push Notifications

---

## Phase 7E — Cloud

Multi Tenant SaaS · Subscriptions · Billing · Monitoring · Backups · Scaling

---

## Sprint Exit Criteria (Standard)

- [ ] Feature code merged
- [ ] Tests pass (`composer quality:gate`)
- [ ] Browser demo works on `localhost:8000`
- [ ] REST APIs documented in module README
- [ ] CHANGELOG updated
- [ ] No architecture freeze violations (or approved ADR)

---

## What NOT To Do

- Do not start new documentation-only sprints
- Do not add architecture docs unless an ADR is required
- Do not skip tests or browser demo for "speed"
- Do not bypass ADT Rule 0 for generated code
- Do not start Phase 5B business modules before 5.A.6 certification

---

## Immediate Next Actions

1. **5.A.6** — Run ADT certification (`php artisan axiomos:make-module Demo --yes` + full validation)
2. **5.A.7–5.A.14** — SDK sprints (code + tests + minimal docs)
3. **5.A.15** — Production freeze sign-off
4. **5.B.1** — Identity module (first true business sprint)
