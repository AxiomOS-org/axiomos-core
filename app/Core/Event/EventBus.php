<?php

declare(strict_types=1);

namespace App\Core\Event;

use App\Core\Event\Contracts\EventBusInterface;
use App\Core\Event\Contracts\EventDispatcherInterface;
use App\Core\Event\Contracts\EventListenerInterface;
use App\Core\Event\Contracts\EventQueueInterface;
use App\Core\Event\Contracts\EventStoreInterface;
use App\Core\Event\Contracts\EventSubscriberInterface;
use App\Core\Event\Contracts\ListenerProviderInterface;
use App\Core\Event\Contracts\QueueableEvent;
use App\Core\Event\Contracts\DelayedEvent;
use App\Core\Event\Events\AfterDispatch;
use App\Core\Event\Events\BeforeDispatch;
use App\Core\Event\Events\DispatchFailed;
use App\Core\Event\Exceptions\EventBusException;
use App\Core\Event\Support\EventEnvelope;
use App\Core\Event\Support\EventMetrics;
use App\Core\Event\Support\RecordedEvent;
use Closure;
use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Enterprise event bus orchestrating synchronous, queued, async and delayed
 * event delivery with priority, wildcards, retry, history and metrics.
 *
 * Domain events are dispatched through an instrumented loop that records
 * timings and emits {@see BeforeDispatch}/{@see AfterDispatch}/{@see DispatchFailed}
 * meta-events. Meta-events themselves are delivered through the pure
 * {@see EventDispatcher} to avoid instrumentation recursion.
 */
final class EventBus implements EventBusInterface
{
    private readonly EventMetrics $metrics;

    private readonly Closure $listenerResolver;

    /**
     * @var array<string, list<array{listener: class-string, priority: int, once: bool}>>
     */
    private array $cacheableMap = [];

    public function __construct(
        private readonly ListenerProviderInterface $listeners,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly EventQueueInterface $queue,
        private readonly EventStoreInterface $store,
        private readonly ?LoggerInterface $logger = null,
        ?callable $listenerResolver = null,
    ) {
        $this->metrics = new EventMetrics();
        $this->listenerResolver = $listenerResolver !== null
            ? Closure::fromCallable($listenerResolver)
            : static fn (string $class): object => new $class();
    }

    public function listen(string $eventPattern, callable $listener, int $priority = 0, bool $once = false): void
    {
        $this->listeners->listen($eventPattern, $listener, $priority, $once);
    }

    /**
     * Register a class-based listener that is resolved lazily and can be cached.
     *
     * @param class-string $listenerClass
     */
    public function listenClass(string $eventPattern, string $listenerClass, int $priority = 0, bool $once = false): void
    {
        $this->cacheableMap[$eventPattern][] = [
            'listener' => $listenerClass,
            'priority' => $priority,
            'once' => $once,
        ];

        $this->listen($eventPattern, $this->makeClassListener($listenerClass), $priority, $once);
    }

    public function subscribe(EventSubscriberInterface $subscriber): void
    {
        foreach ($subscriber->subscribe() as $eventPattern => $definition) {
            $this->registerSubscription($eventPattern, $definition);
        }
    }

    public function dispatch(object $event): object
    {
        if ($event instanceof QueueableEvent) {
            $this->enqueue($event, $event->maxAttempts(), $this->delayFor($event));

            return $event;
        }

        return $this->dispatchNow($event);
    }

    public function dispatchAsync(object $event, int $maxAttempts = 1): void
    {
        $this->enqueue($event, $maxAttempts, 0);
    }

    public function dispatchDelayed(object $event, int $delaySeconds, int $maxAttempts = 1): void
    {
        $this->enqueue($event, $maxAttempts, max(0, $delaySeconds));
    }

    public function processQueue(?float $now = null): int
    {
        $now ??= microtime(true);
        $handled = 0;

        while (($envelope = $this->queue->pop($now)) !== null) {
            try {
                $this->dispatchNow($envelope->event);
                $handled++;
            } catch (Throwable $exception) {
                $this->handleQueueFailure($envelope, $exception, $now);
            }
        }

        return $handled;
    }

    public function metrics(): EventMetrics
    {
        return $this->metrics;
    }

    public function cache(string $path): void
    {
        $exported = var_export($this->cacheableMap, true);
        $result = file_put_contents($path, "<?php\n\ndeclare(strict_types=1);\n\nreturn {$exported};\n");

        if ($result === false) {
            throw new EventBusException(sprintf('Unable to write event cache to "%s".', $path));
        }
    }

    public function loadCache(string $path): bool
    {
        if (! is_file($path)) {
            return false;
        }

        /** @var array<string, list<array{listener: class-string, priority: int, once: bool}>> $map */
        $map = require $path;

        foreach ($map as $eventPattern => $registrations) {
            foreach ($registrations as $registration) {
                $this->listenClass(
                    $eventPattern,
                    $registration['listener'],
                    $registration['priority'],
                    $registration['once'],
                );
            }
        }

        return true;
    }

    private function dispatchNow(object $event): object
    {
        $eventName = $event::class;
        $stoppable = $event instanceof StoppableEventInterface;
        $count = 0;
        $startedAt = hrtime(true);

        $this->fireMeta(new BeforeDispatch($event, $eventName));

        try {
            foreach ($this->listeners->getListenersForEvent($event) as $listener) {
                if ($stoppable && $event->isPropagationStopped()) {
                    break;
                }

                $listener($event);
                $count++;
            }
        } catch (Throwable $exception) {
            $this->recordFailure($event, $eventName, $exception, $startedAt);

            throw $exception;
        }

        $durationMs = $this->elapsedMs($startedAt);
        $this->metrics->recordDispatch($eventName, $count, $durationMs);
        $this->store->record(new RecordedEvent($eventName, $count, $durationMs, true, microtime(true)));
        $this->fireMeta(new AfterDispatch($event, $eventName, $count, $durationMs));

        return $event;
    }

    private function enqueue(object $event, int $maxAttempts, int $delaySeconds): void
    {
        $this->queue->push(new EventEnvelope(
            event: $event,
            attempts: 0,
            maxAttempts: max(1, $maxAttempts),
            availableAt: microtime(true) + $delaySeconds,
        ));

        $this->metrics->recordQueued();
    }

    private function handleQueueFailure(EventEnvelope $envelope, Throwable $exception, float $now): void
    {
        $this->logger?->error('Queued event failed.', [
            'event' => $envelope->event::class,
            'attempts' => $envelope->attempts + 1,
            'exception' => $exception->getMessage(),
        ]);

        $retried = $envelope->nextAttempt($now);

        if ($retried->canRetry()) {
            $this->queue->push($retried);
            $this->metrics->recordRetry();
        }
    }

    private function recordFailure(object $event, string $eventName, Throwable $exception, int $startedAt): void
    {
        $durationMs = $this->elapsedMs($startedAt);
        $this->metrics->recordFailure();
        $this->store->record(new RecordedEvent(
            eventName: $eventName,
            listenerCount: 0,
            durationMs: $durationMs,
            succeeded: false,
            occurredAt: microtime(true),
            error: $exception->getMessage(),
        ));

        $this->logger?->error('Event dispatch failed.', [
            'event' => $eventName,
            'exception' => $exception->getMessage(),
        ]);

        $this->fireMeta(new DispatchFailed($event, $eventName, $exception));
    }

    private function fireMeta(object $metaEvent): void
    {
        $this->dispatcher->dispatch($metaEvent);
    }

    /**
     * @param class-string $listenerClass
     *
     * @return callable(object): void
     */
    private function makeClassListener(string $listenerClass): callable
    {
        return function (object $event) use ($listenerClass): void {
            $listener = ($this->listenerResolver)($listenerClass);

            if (! $listener instanceof EventListenerInterface) {
                throw new EventBusException(sprintf(
                    'Listener "%s" must implement %s.',
                    $listenerClass,
                    EventListenerInterface::class,
                ));
            }

            $listener->handle($event);
        };
    }

    /**
     * @param callable|array{0: callable, 1: int}|list<callable|array{0: callable, 1: int}> $definition
     */
    private function registerSubscription(string $eventPattern, mixed $definition): void
    {
        if (is_callable($definition)) {
            $this->listen($eventPattern, $definition);

            return;
        }

        if (is_array($definition) && $this->isPrioritisedCallable($definition)) {
            $this->listen($eventPattern, $definition[0], $definition[1]);

            return;
        }

        if (is_array($definition)) {
            foreach ($definition as $item) {
                $this->registerSubscription($eventPattern, $item);
            }

            return;
        }

        throw new EventBusException(sprintf('Invalid subscription definition for "%s".', $eventPattern));
    }

    /**
     * @param array<int|string, mixed> $definition
     *
     * @phpstan-assert-if-true array{0: callable, 1: int} $definition
     */
    private function isPrioritisedCallable(array $definition): bool
    {
        return array_is_list($definition)
            && count($definition) === 2
            && is_callable($definition[0])
            && is_int($definition[1]);
    }

    private function delayFor(QueueableEvent $event): int
    {
        return $event instanceof DelayedEvent ? max(0, $event->delaySeconds()) : 0;
    }

    private function elapsedMs(int $startedAt): float
    {
        return (hrtime(true) - $startedAt) / 1_000_000;
    }
}
