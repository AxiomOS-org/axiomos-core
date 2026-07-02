# 040 AI Orchestrator

## Purpose
Coordinate autonomous software engineering agents across the full ADT pipeline.

## Agent Flow
Business Requirement -> Planner Agent -> Architect Agent -> Database Agent -> Backend Agent -> Frontend Agent -> QA Agent -> Security Agent -> Documentation Agent -> Reviewer Agent -> Merge

## Two AI Layers
- **Developer AI**: code generation, refactoring, tests, architecture review.
- **Business AI**: voice assistant, reports, analytics, automation, forecasting, chat.

## Rules
- Agents must not bypass architecture validator.
- Agents must run in simulation mode before file writes.
- Human approval required for merge-ready output.

## Canonical Architecture
See `docs/architecture/AI_ORCHESTRATOR_ARCHITECTURE.md`.
