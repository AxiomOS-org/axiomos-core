<?php

declare(strict_types=1);

namespace App\Core\Event\Events;

use Throwable;

/**
 * Meta-event fired when a listener throws during dispatch.
 */
final readonly class DispatchFailed
{
    public function __construct(
        public object $event,
        public string $eventName,
        public Throwable $exception,
    ) {
    }
}
