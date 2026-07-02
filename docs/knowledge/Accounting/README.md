# Accounting Knowledge Pack

**Authority:** `docs/architecture/ACCOUNTING_BLUEPRINT_LOCK.md` (ACC-BP-1.0)

## Scope
Enterprise GL kernel: double entry, universal COA, posting engine, ledger immutability, multi-company/currency/FY/branch, dimensions, tax, audit.

## Prime Rule
All business modules post via **Accounting Posting Engine** only. No direct ledger writes.

## AI Usage
Load before any Accounting implementation or financial integration in Sales/Purchase/Inventory/POS/Payroll/Manufacturing/Assets.

## Locked Sections
- Posting Engine contract (`PostingEngineInterface`)
- Document lifecycle: draft → pending_approval → approved → posted → locked / reversed / cancelled
- Engine stack: Document → Posting → Journal → Ledger
- Protected tables: `accounting_*` (Accounting Infrastructure only)
- Phase 6.A.1–6.A.15 structure in blueprint

## Integration
Source modules submit `PostingRequest` with idempotency key `{module}:{type}:{id}`.
