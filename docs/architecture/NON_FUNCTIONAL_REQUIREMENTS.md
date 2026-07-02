# Non-Functional Requirements (Frozen Targets)

## Availability
- Target: 99.9% monthly for core APIs.

## Scalability
- Horizontal scaling support for stateless API layer.
- Data model and indexing strategy prepared for 10M+ records.

## Security
- OWASP Top 10 baseline coverage in design and testing.
- Least privilege and auditability by default.

## Performance
- Target: critical API P95 under 100ms for non-reporting endpoints under baseline load.

## Reliability
- Deterministic boot and dependency resolution.
- Controlled failure modes for module/plugin issues.

## Observability
- Standardized health, metrics, and structured error logging.

## Disaster Recovery & Backup
- Defined backup schedule and restore test cadence.
- Recovery procedures documented and tested.

## Audit & Compliance
- Audit trails for sensitive business actions.
- Data lifecycle and retention policies documented per module.

