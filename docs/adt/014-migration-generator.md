# 014 Migration Generator

## Purpose
Generate PostgreSQL-first migrations with rollback safety.

## Input
- Table/entity metadata
- Index strategy
- Constraint profile

## Output
- Migration scripts
- Schema notes
- Migration tests

## Rules
- No unsafe destructive migration by default.
- Index and constraint hints mandatory.
