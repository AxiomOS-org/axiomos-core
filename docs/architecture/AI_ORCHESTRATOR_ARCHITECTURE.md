# AI Orchestrator Architecture

## Vision
ADT as an **Autonomous Software Engineering Platform**, not a simple generator.

## End-to-End Pipeline
Business Requirement -> AI Requirement Analyzer -> Architecture Validator -> DDD Validator -> Database Designer -> API Planner -> UI Planner -> Code Generator -> Test Generator -> Static Analysis -> Performance Analysis -> Security Analysis -> Documentation -> Code Review -> Ready Module

## Agent Roles
| Agent | Responsibility |
|---|---|
| Planner Agent | Requirement decomposition and blueprint |
| Architect Agent | Freeze/governance compliance |
| Database Agent | Schema and migration planning |
| Backend Agent | Services, repos, APIs |
| Frontend Agent | UI planner and presentation scaffolds |
| QA Agent | Test planning and generation |
| Security Agent | Security analysis and findings |
| Documentation Agent | Docs and changelog |
| Reviewer Agent | Merge readiness decision |

## AI Layers (Strict Separation)
### Layer 1 — Developer AI
For engineers: code generation, refactoring, tests, architecture review, ADT orchestration.

### Layer 2 — Business AI
For ERP users: voice assistant, reports, analytics, automation, workflow, forecasting, chat.

**Rule:** Developer AI and Business AI must not share runtime context or credentials.

## Simulation-First
All agent outputs pass through simulation mode before file writes (see `ADT_SIMULATION_MODE.md`).

## Knowledge Integration
Agents must consult `docs/knowledge/<domain>/` before domain-specific generation.

## Sprint-End Reports (Auto-Generated)
- Architecture Compliance Report
- Technical Debt Report
- Test Coverage Report
- Security Report
- Performance Report
- ADR Changes Report
- Documentation Coverage Report
- AI Confidence Report
