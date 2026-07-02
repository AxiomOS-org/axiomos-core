<?php

declare(strict_types=1);

namespace App\Core\Event\Events;

/**
 * Meta-event fired after an event has been dispatched to all listeners.
 */
final readonly class AfterDispatch
{
    public function __construct(
        public object $event,
        public string $eventName,
        public int $listenerCount,
        public float $durationMs,
    ) {
    }
}
