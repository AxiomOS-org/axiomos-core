# 011 Validation Rules

## Input Validation
- Validate all external input at boundary layer.
- Use module-specific validators for create/update/use-case commands.

## Rules
- Enforce type, format, length, and domain constraints.
- Use allow-lists for writable fields.
- Normalize before persistence (trim, casing, canonical forms).

## Output Validation
- Critical outbound integrations must validate payload contracts.
