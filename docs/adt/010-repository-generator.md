# 010 Repository Generator

## Purpose
Generate repository contracts and infrastructure implementations.

## Input
- Entity and query capabilities
- Read/write behavior profile

## Output
- Domain repository interface
- Infrastructure persistence adapter
- Repository tests

## Rules
- Interface in Domain, implementation in Infrastructure.
- No request object leakage into repository signatures.
