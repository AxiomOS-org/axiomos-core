# Sprint Governance Reports

## Purpose
At the end of every sprint, AI (or CI orchestration) must generate these reports automatically.

## Required Reports
1. **Architecture Compliance Report**
2. **Technical Debt Report**
3. **Test Coverage Report**
4. **Security Report**
5. **Performance Report**
6. **ADR Changes Report**
7. **Documentation Coverage Report**
8. **AI Confidence Report**

## Output Location
```
docs/reports/sprint-<sprint-id>/
  01-architecture-compliance.md
  02-technical-debt.md
  03-test-coverage.md
  04-security.md
  05-performance.md
  06-adr-changes.md
  07-documentation-coverage.md
  08-ai-confidence.md
```

## Report Template (each file)
```markdown
# <Report Name> — Sprint <id>

## Summary
- Status: Pass | Warning | Fail
- Generated At: <timestamp>
- Scope: <modules/files>

## Findings
- <finding>

## Metrics
- <metric>: <value>

## Actions
- <action item>

## AI Confidence
- Score: 0-100
- Rationale: <short explanation>
```

## Gate Policy
- Sprint cannot close with unresolved `Fail` in Architecture Compliance or Security reports.
