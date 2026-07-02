# AxiomOS Enterprise Accounting Blueprint — LOCKED

**Lock ID:** ACC-BP-1.0  
**Status:** 🔒 LOCKED — Design authority for Phase 6.A  
**Effective:** 2026-07-02  
**Scope:** All financial logic, ledger schema, posting contracts, and ERP integrations  
**Approved by:** Project Architect (pending sign-off)

---

## Prime Directive

> **Accounting is not a consumer of other modules.**  
> **Every business module is a consumer of Accounting.**

Sales, Purchase, Inventory, POS, Payroll, Manufacturing, Fixed Assets, Projects, and CRM **never write ledger data directly**. They submit **posting intents** to the **Accounting Posting Engine**, which is the **only** component allowed to create journals and ledger lines.

```
Sales ──────────────┐
Purchase ───────────┤
Inventory ──────────┤
POS ────────────────┼──► Accounting Posting Engine ──► Journal Engine ──► Ledger Engine
Payroll ────────────┤         (single write path)
Manufacturing ──────┤
Fixed Assets ───────┘
```

---

## Non-Negotiable Rules (LOCKED)

### Rule ACC-1 — Posting Engine Gate

```
NO ERP MODULE IS ALLOWED TO WRITE TO LEDGER TABLES DIRECTLY.
ALL FINANCIAL POSTINGS MUST PASS THROUGH THE ACCOUNTING POSTING ENGINE.
NO EXCEPTIONS.
```

| Allowed | Forbidden |
|---------|-----------|
| `PostingEngine::submit(PostingRequest)` | `INSERT` into `accounting_*` ledger tables from Sales/Inventory/etc. |
| `PostingEngine::reverse(ReversalRequest)` | Eloquent `JournalLine::create()` outside Accounting module |
| Read-only GL queries via Accounting API | Shared repository writing to `general_ledger` |
| Idempotent posting with `source_document_id` | Bypassing document lifecycle (Draft → Posted) |

**Enforcement:** `tests/Architecture/LedgerWriteProtectionTest.php` + architecture-rules scan. CI fails on violation.

### Rule ACC-2 — Double Entry Invariant

Every posted journal MUST satisfy:

- Σ debits = Σ credits (in document currency and company functional currency)
- Minimum 2 lines per journal
- No line with both debit and credit > 0
- Period must be **open** unless engine is `ClosingEngine` or `OpeningBalanceEngine`

### Rule ACC-3 — Immutability After Post

Posted ledger lines are **append-only**. Corrections use **Reversal Engine** or **Adjustment Engine** — never `UPDATE` on posted amounts.

### Rule ACC-4 — Universal COA

One **Chart of Accounts** tree per company, shared across all modules. Module-specific accounts are **COA segments**, not separate charts.

### Rule ACC-5 — Document Before Ledger

No ledger write without a **Document** in state `approved` (or auto-approved policy) transitioning to `posted` via Posting Engine.

---

## Foundation

| Capability | Specification |
|------------|---------------|
| **Double Entry Accounting** | Debit/credit only; no single-sided postings |
| **Universal Chart of Accounts** | Hierarchical COA: type → category → group → account; codes unique per company |
| **Multi-Company** | Company-scoped COA, journals, periods, series; consolidated reporting optional |
| **Multi-Currency** | Document currency + functional currency + exchange rate snapshot at post time |
| **Multi-Fiscal Year** | Fiscal years per company; non-overlapping date ranges |
| **Multi-Branch** | Branch dimension on documents and lines; branch-level series and TB |
| **Cost Centers** | Mandatory or optional analytic dimension per account/voucher type |
| **Profit Centers** | Analytic dimension for P&L segmentation |
| **Dimensions** | Extensible dimension slots (project, department, product line, custom) |
| **Projects** | Project ID as dimension; WIP and capitalization rules via posting profiles |
| **Budgets** | Budget versions per fiscal year; optional hard/soft stop at posting |

---

## Engines (Accounting Kernel)

| Engine | Responsibility |
|--------|----------------|
| **Ledger Engine** | Maintains general ledger balances; account running totals; period balances |
| **Journal Engine** | Composes balanced journal headers + lines from validated posting payloads |
| **Posting Engine** | **Single entry point** for all modules; validates, idempotency, dispatches to Journal Engine |
| **Reversal Engine** | Creates contra journal linked to original; marks document `reversed` |
| **Adjustment Engine** | Period adjustments, audit corrections, reclassification journals |
| **Closing Engine** | Period/year close; locks periods; generates closing entries |
| **Opening Balance Engine** | OB journals for new fiscal year / new company go-live |
| **Document Engine** | Lifecycle, attachments, approvals, numbering, linkage to source modules |

### Engine Dependency Order

```
Document Engine → Posting Engine → Journal Engine → Ledger Engine
                      ↑
              Reversal / Adjustment / Closing / Opening Balance
```

---

## Document Lifecycle (LOCKED)

```
draft → pending_approval → approved → posted → locked
                              ↓
                         cancelled (pre-post only)
posted → reversed (via Reversal Engine)
```

| State | Ledger impact | Editable |
|-------|---------------|----------|
| `draft` | None | Yes |
| `pending_approval` | None | Limited |
| `approved` | None | No (await post) |
| `posted` | **Written** | No |
| `locked` | Frozen | No |
| `cancelled` | None | Terminal |
| `reversed` | Contra posted | No |

---

## Numbering (LOCKED)

| Series | Scope | Example pattern |
|--------|-------|-----------------|
| **Voucher Series** | Per voucher type + company | `JV/{FY}/{SEQ}` |
| **Fiscal Series** | Resets each fiscal year | `FY2026-000001` |
| **Branch Series** | Optional branch prefix | `{BR}-INV-000123` |
| **Company Series** | Company code prefix | `ACME-PO-000456` |

Numbering is **server-assigned** at `approved` or `posted` transition — never client-supplied final numbers.

---

## Tax Engine (Regional)

| Component | Role |
|-----------|------|
| **VAT** | Output/input tax accounts; tax lines on journals |
| **GST** | CGST/SGST/IGST split profiles (region config) |
| **Withholding** | WHT on payments; liability accounts |
| **Regional Tax Engine** | Pluggable tax calculators per `company.region`; posting profiles map tax to COA |

Tax amounts are **journal lines**, not side tables written by source modules.

---

## Audit & Compliance (LOCKED)

| Requirement | Implementation |
|-------------|----------------|
| **Immutable Ledger** | Posted lines: no UPDATE/DELETE; soft-delete prohibited on ledger |
| **Complete Audit Trail** | Platform `universal_audit_logs` + accounting-specific post log |
| **Version History** | Document snapshots at approve/post via Platform versioning |
| **Digital Signature Ready** | `posted_by`, `posted_at`, `signature_hash` nullable fields on journal header |

---

## Module Architecture (DDD + Hexagonal)

```
modules/Accounting/
  Domain/
    Models/           # Journal, JournalLine, Account, FiscalPeriod, ...
    Events/           # JournalPosted, PeriodClosed, ...
    Repositories/Contracts/
    ValueObjects/     # Money, AccountCode, PostingIdempotencyKey
    Services/Contracts/  # PostingEngineInterface (port)
  Application/
    Services/         # PostingEngine, LedgerEngine, ClosingEngine, ...
    DTOs/             # PostingRequest, PostingResult, ReversalRequest
    PostingProfiles/  # SalesInvoiceProfile, GrnProfile, PayrollProfile
  Infrastructure/
    Persistence/      # ONLY layer that touches accounting_* tables
  Http/
    Controllers/Api/  # Read APIs + posting command endpoints (Accounting only)
```

**Other modules** may only depend on:

- `Modules\Accounting\Application\DTOs\*`
- `Modules\Accounting\Domain\Services\Contracts\PostingEngineInterface`
- Accounting **read** query APIs (trial balance, account balance)

They **must not** import `Infrastructure\Persistence\*` from Accounting.

---

## Posting Engine Contract (Port)

```php
// Conceptual — implementation in 6.A.7
interface PostingEngineInterface
{
    public function submit(PostingRequest $request): PostingResult;
    public function reverse(ReversalRequest $request): PostingResult;
    public function preview(PostingRequest $request): PostingPreview; // no ledger write
}
```

### PostingRequest (minimum fields)

| Field | Purpose |
|-------|---------|
| `idempotency_key` | `{source_module}:{source_document_type}:{source_document_id}` |
| `source_module` | `Sales`, `Inventory`, ... |
| `source_document_id` | UUID of originating document |
| `company_id` | Company scope |
| `branch_id` | Optional branch |
| `posting_date` | Accounting date |
| `currency` | Document currency |
| `exchange_rate` | Snapshot if foreign |
| `lines[]` | account_id, debit, credit, dimensions, tax_code |
| `voucher_type` | Maps to journal type + series |

Posting Engine returns `journal_id` + `document_number` or structured validation errors — **never** partial posts.

---

## Protected Database Tables

Only `modules/Accounting/Infrastructure/**` may reference:

- `accounting_accounts` (COA)
- `accounting_fiscal_years`
- `accounting_periods`
- `accounting_voucher_types`
- `accounting_documents`
- `accounting_journals`
- `accounting_journal_lines`
- `accounting_ledger_balances`
- `accounting_posting_log`
- `accounting_tax_profiles`
- `accounting_dimensions`
- `accounting_budgets`

---

## Phase 6.A — Single-Run Structure

**Current phase:** Design lock only. **No CRUD, no voucher UI, no journal entry screens.**

| ID | Deliverable | Design (this blueprint) | Code (next run after sign-off) |
|----|-------------|-------------------------|--------------------------------|
| **6.A.1** | Accounting Architecture | ✅ Locked in this document | Module skeleton + engine interfaces |
| **6.A.2** | Chart of Accounts | COA hierarchy spec above | COA tables + API |
| **6.A.3** | Fiscal Years | Multi-FY spec | Fiscal year entity + migrations |
| **6.A.4** | Accounting Periods | Period open/close rules | Period entity + lock flags |
| **6.A.5** | Journals | Journal header/line model | Journal aggregate |
| **6.A.6** | Voucher Types | Series + type mapping | Voucher type config |
| **6.A.7** | Posting Engine | Contract above | **Core implementation** |
| **6.A.8** | Ledger Engine | Balance rules | Ledger write path |
| **6.A.9** | Trial Balance | TB query spec | Read API |
| **6.A.10** | Balance Sheet | BS mapping from COA types | Report API |
| **6.A.11** | Profit & Loss | P&L mapping | Report API |
| **6.A.12** | Cash Flow | Indirect method mapping | Report API |
| **6.A.13** | Multi-Currency | Rate snapshot rules | FX tables + conversion |
| **6.A.14** | Cost Centers | Dimension spec | Analytic dimensions |
| **6.A.15** | Production Review | Gate + sign-off | Full `quality:gate` |

---

## ERP Build Order (Post 6.A)

| Phase | Module | Posts via |
|-------|--------|-----------|
| **6.A** | Accounting Foundation | — (kernel) |
| **6.B** | Sales | Posting Engine |
| **6.C** | Purchase | Posting Engine |
| **6.D** | Inventory | Posting Engine |
| **6.E** | CRM | Posting Engine (if financial) |
| **6.F** | HR & Payroll | Posting Engine |
| **6.G** | Manufacturing (MRP) | Posting Engine |
| **6.H** | POS | Posting Engine |
| **6.I** | Projects | Posting Engine |
| **6.J** | Fixed Assets | Posting Engine |
| **6.K** | Budgeting | Reads Accounting; posts adjustments |
| **6.L** | Reporting & BI | Read-only from Accounting |

---

## What Is Explicitly Out of Scope (Until 6.A Code Sprint)

- Journal entry screens / voucher UI
- CRUD admin pages for COA (design only now)
- Sales/Purchase/Inventory module code
- Direct SQL or Eloquent ledger writes outside Accounting

---

## Sign-Off Checklist

- [ ] Architect approves ACC-BP-1.0
- [ ] Posting Engine contract frozen
- [ ] Protected table list frozen
- [ ] CI ledger write protection enabled
- [ ] Implementation sprint authorized (6.A code)

**Next document:** `docs/reports/PHASE_6A_DESIGN_LOCK_REPORT.md`
