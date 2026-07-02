# ADR — Enterprise Event Bus (Sprint 4.7)

## Status

Accepted — 2026-07-02

## Context

AxiomOS is event-driven by design (architecture rule 003). Core components already emit domain events through `Illuminate\Contracts\Events\Dispatcher`, but the platform needs a native, PSR-14 compatible bus supporting priority, wildcards, queued/async/delayed delivery, retry, history and metrics — the decoupling backbone for modules (Auth → Audit, etc.).

## Decision

### Separation of concerns

| Class | Responsibility |
|---|---|
| `ListenerProvider` | Store + resolve listeners (priority, wildcard, once) — PSR-14 provider |
| `EventDispatcher` | Pure synchronous PSR-14 dispatch loop (stoppable) |
| `EventBus` | Enterprise façade: queue/async/delayed, retry, history, metrics, meta-events |
| `InMemoryEventQueue` | Availability-ordered queue backend (swappable) |
| `InMemoryEventStore` | Bounded ring-buffer history |
| `EventDiscovery` | Register subscriber classes (module scanning) |
| `EventBusBuilder` | Wire a ready-to-use bus |

### PSR-14 compatibility

`ListenerProvider` and `EventDispatcher` implement the PSR-14 interfaces, so third-party PSR-14 consumers interoperate. AxiomOS extensions (priority, wildcard, once) are additive.

### Priority + ordering

Higher priority runs first; ties break by registration order (stable sort).

### Wildcards

`*` matches all domain events; `Namespace\*` prefix-matches. **Framework meta-events (`App\Core\Event\Events\*`) are excluded from wildcard matching** to prevent per-dispatch amplification and recursion surprises — they are delivered only to explicit listeners.

### Meta-events

`BeforeDispatch`, `AfterDispatch`, `DispatchFailed` are dispatched through the pure `EventDispatcher` (not the instrumented bus path), avoiding infinite recursion and metric pollution.

### Queue / async / delayed

- Events implementing `QueueableEvent` are enqueued by `dispatch()` automatically.
- `dispatchAsync()` forces any event onto the queue.
- `dispatchDelayed()` / `DelayedEvent` sets availability time.
- `processQueue()` drains due envelopes (worker/scheduler tick, Octane, RoadRunner).

### Retry

Envelopes carry `attempts`/`maxAttempts`. Failed queued dispatch re-enqueues until the bound is reached; each retry is counted in metrics.

### Cache

`cache()` / `loadCache()` persist **class-based** listener registrations (via `listenClass()`) to a PHP return file. Closures are intentionally not cached.

## Alternatives rejected

| Alternative | Why rejected |
|---|---|
| Wrap Illuminate Dispatcher only | Not PSR-14 first; weaker control over queue/retry/metrics semantics |
| Symfony EventDispatcher dependency | Heavier; AxiomOS wants a thin, owned core |
| Fire meta-events through the instrumented path | Infinite recursion; metric pollution |
| Wildcards match meta-events | Per-dispatch amplification (observed 3× in tests) |
| Serialize closures for cache | Not portable/safe; class listeners only |

## Trade-offs

| Benefit | Cost |
|---|---|
| PSR-14 + enterprise features | Small dispatch-loop duplication (pure vs instrumented) |
| In-memory queue/store | Not durable across processes (swappable interfaces provided) |
| Meta-events on every dispatch | Minor overhead; mitigated by exclusion rule |

## Future extension points

- Redis/database `EventQueueInterface` for cross-process async
- Persistent `EventStoreInterface` (event sourcing / audit)
- Exponential backoff for retries
- Attribute-based listener discovery (`#[On(EventClass::class)]`)
- OpenTelemetry exporter fed by `EventMetrics`

## Test evidence

116 tests, 264 assertions — all passing. Benchmarks within budget.
