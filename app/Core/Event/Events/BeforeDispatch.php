<?php

declare(strict_types=1);

namespace App\Core\Event\Events;

/**
 * Meta-event fired before an event is dispatched to its listeners.
 */
final readonly class BeforeDispatch
{
    public function __construct(
        public object $event,
        public string $eventName,
    ) {
    }
}
