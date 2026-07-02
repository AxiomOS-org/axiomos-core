# ADT Architecture

## Vision
ADT is an **Autonomous Software Engineering Platform**, not only a generator.

## End-to-End Pipeline
Business Requirement
-> AI Requirement Analyzer
-> Architecture Validator
-> DDD Validator
-> Database Designer
-> API Planner
-> UI Planner
-> Code Generator
-> Test Generator
-> Static Analysis
-> Performance Analysis
-> Security Analysis
-> Documentation
-> Code Review
-> Ready Module

## AI Orchestrator
The orchestrator coordinates specialized agents (Planner, Architect, Database, Backend, Frontend, QA, Security, Documentation, Reviewer) and enforces simulation-first writes.

See:
- `docs/architecture/AI_ORCHESTRATOR_ARCHITECTURE.md`
- `docs/adt/040-ai-orchestrator.md`

## Simulation-First Rule
No generator may write files directly. Required flow:
Generate -> Preview -> Impact Analysis -> Dependency Analysis -> Conflict Detection -> Approval -> Write Files

See `docs/adt/ADT_SIMULATION_MODE.md`.

## Knowledge and Prompts
- Domain knowledge: `docs/knowledge/`
- Standard prompts: `docs/prompts/`

## Manifest Contract
All generated packages must conform to AMS:
`docs/architecture/AMS_SPECIFICATION.md`

## Input
- Requirement spec (human or AI planner)
- Standards profile
- Target module metadata
- Domain knowledge pack (when applicable)

## Output
- Generated artifact set
- Validation and quality reports
- Documentation bundle
- Sprint governance reports (`docs/reports/SPRINT_REPORT_TEMPLATE.md`)

## Extensibility
- Plugin and marketplace extension points
- Transformer chain hooks for evolution
- Upgrade engine compatibility via AMS
