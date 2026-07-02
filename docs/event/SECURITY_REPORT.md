# SECURITY REPORT — Event Bus (Sprint 4.7)

## Findings

1. **Listener cache uses `require`** — `loadCache()` executes a PHP file. Cache directory must be trusted and not world-writable.
2. **Class-based listeners are instantiated by class-string** — only trusted, autoloaded classes should reach `listenClass()`/cache; never pass user input as a listener class.
3. **Listener resolver injection** — resolution goes through an injected factory (usually the container); the same trust boundary as the container applies.
4. **Event history may capture sensitive payloads** — `RecordedEvent` stores event name + error message only, not the event object, reducing leakage. Error messages could still contain sensitive data.
5. **Meta-events isolation** — wildcard listeners cannot observe framework meta-events, reducing accidental interception of lifecycle signals.
6. **No serialization of events for the in-memory queue** — avoids object-injection; a future durable queue must serialize safely.

## Risks

| Risk | Severity | Likelihood |
|---|---|---|
| Poisoned listener cache file | High | Low (needs FS write) |
| User-controlled listener class | High if misused | Low (not exposed) |
| Sensitive data in error history | Medium | Medium |
| Durable queue deserialization (future) | High | N/A yet |

## Recommendations

1. Restrict event cache path permissions to the deploy user.
2. Never expose `listen()/listenClass()` to request input.
3. Redact/limit error messages stored in history for production.
4. When adding a durable queue, use signed/whitelisted payload deserialization.

## Status

**Pass** — safe under intended usage (trusted listeners, trusted cache path, in-memory queue).
