# Backward Compatibility Policy

## Semantic Versioning
- MAJOR: breaking API/contract changes
- MINOR: backward-compatible functionality
- PATCH: backward-compatible fixes only

## Compatibility Scope
- Public API routes/payloads
- Module manifest contract
- Core/platform extension contracts
- Event payload schemas

## Deprecation Strategy
1. Mark deprecated in docs and changelog.
2. Provide compatibility window (minimum 2 minor releases unless emergency).
3. Emit runtime deprecation warnings where possible.
4. Remove only in next major release with migration guide.

## Change Control
- Any incompatible change requires ADR + migration plan + rollback notes.

