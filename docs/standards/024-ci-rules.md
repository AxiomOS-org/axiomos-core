# 024 CI Rules

## Required Pipeline Order
- Lint
- Static Analysis
- Architecture Rules
- Unit Tests
- Module Tests
- Integration Tests
- Performance Smoke
- Security Scan

## Rules
- Any failed gate blocks merge.
- CI configuration must be reproducible locally.
- CI must run on pull requests and protected branches.
