<?php

declare(strict_types=1);

namespace App\Core\Event\Contracts;

use App\Core\Event\Support\EventMetrics;

/**
 * High-level enterprise event bus.
 *
 * Unifies synchronous dispatch, subscriber/listener registration, queued/async/
 * delayed events, retry, history and metrics behind a single façade.
 */
interface EventBusInterface
{
    /**
     * Register a listener for an event name or wildcard pattern.
     *
     * @param callable(object): void $listener
     */
    public function listen(string $eventPattern, callable $listener, int $priority = 0, bool $once = false): void;

    public function subscribe(EventSubscriberInterface $subscriber): void;

    /**
     * Dispatch an event. Queueable events are enqueued; others run synchronously.
     *
     * @template T of object
     *
     * @param T $event
     *
     * @return T
     */
    public function dispatch(object $event): object;

    /**
     * Force an event onto the queue regardless of whether it is queueable.
     */
    public function dispatchAsync(object $event, int $maxAttempts = 1): void;

    /**
     * Enqueue an event to be handled after a delay in seconds.
     */
    public function dispatchDelayed(object $event, int $delaySeconds, int $maxAttempts = 1): void;

    /**
     * Process due queued events, returning the number successfully handled.
     */
    public function processQueue(?float $now = null): int;

    public function metrics(): EventMetrics;
}
