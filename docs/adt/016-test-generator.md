# 016 Test Generator

## Purpose
Generate tests for all pyramid layers.

## Input
- Artifact type
- Critical paths
- Risk profile

## Output
- Unit/module/integration/e2e/performance/security tests

## Strategy
- Default to focused deterministic tests.
- Generate fixtures and factory bindings with each test set.
