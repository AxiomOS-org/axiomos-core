# 020 AI Rules

## AI Integration Principles
- AI features must be explainable, auditable, and bounded.
- AI context storage must respect tenancy and data classification.

## Rules
- No direct model calls from controllers; use application services.
- Prompt templates and model settings must be versioned.
- Sensitive data redaction is required before AI submission.
- AI outputs must pass validation before persistence.

## Governance
- Log AI actions in audit/timeline where relevant.
