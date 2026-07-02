# Layer Rules

## Domain
- Contains only entities, value objects, repository interfaces, domain services, domain events.
- No framework or persistence implementation code.

## Application
- Contains use cases, DTOs, commands, queries, orchestration services.
- Owns transactional intent and business workflow composition.

## Infrastructure
- Contains adapters: Eloquent repositories, external clients, persistence mappers, listeners.
- Implements contracts defined in Domain/Application.

## Presentation
- Web UX concerns only (views/pages/controllers for browser rendering).
- No business logic.

## API
- API controllers/resources/request validation only.
- Must delegate business behavior to Application services.

## Platform
- Cross-cutting services shared by modules (audit, timeline, notifications, etc.).
- Must not host business-domain rules for specific ERP modules.

## Core
- Kernel/runtime concerns only.
- No business feature logic.

