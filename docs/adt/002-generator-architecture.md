# 002 Generator Architecture

## Layers
1. Input Layer (CLI/API/AI prompts)
2. Planning Layer (module blueprint + dependency map)
3. Validation Layer (standards + architecture freeze rules)
4. Generation Layer (templates + transformers)
5. Verification Layer (lint/tests/static checks)
6. Output Layer (files, docs, reports)

## Design Principles
- Deterministic output for same inputs.
- Idempotent generation runs.
- Pluggable generators per artifact type.
- Full traceability via generation logs.
