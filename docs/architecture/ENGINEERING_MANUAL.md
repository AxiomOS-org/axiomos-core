# AxiomOS Engineering Manual

## Purpose
Official handbook for all engineers and AI assistants working on AxiomOS.

## Mandatory Reading Order
1. `ARCHITECTURE_FREEZE.md`
2. `MODULE_BOUNDARIES.md`
3. `DEPENDENCY_RULES.md`
4. `LAYER_RULES.md`
5. `BACKWARD_COMPATIBILITY.md`
6. `../standards/` (all standards files)

## Delivery Workflow
1. Confirm scope and out-of-scope.
2. Check affected standards.
3. Implement within allowed boundaries.
4. Run required quality gates.
5. Produce sprint-end governance reports.

## ADR Requirement
- Any change to frozen architecture parts requires approved ADR before merge.

## Sprint-End Mandatory Reports
- Architecture Compliance Report
- Quality Report
- Performance Report
- Technical Debt Report

## Enforcement
- Pull requests without standards compliance evidence are not mergeable.

