<?php

declare(strict_types=1);

namespace App\Core\Event\Contracts;

/**
 * A queueable event that should only become available after a delay.
 */
interface DelayedEvent extends QueueableEvent
{
    /**
     * Number of seconds to wait before the event becomes available for handling.
     */
    public function delaySeconds(): int;
}
