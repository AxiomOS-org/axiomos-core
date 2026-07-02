<?php

declare(strict_types=1);

namespace App\Core\Event\Contracts;

/**
 * Marks an event that should be handled off the synchronous dispatch path.
 *
 * When dispatched through the event bus, queueable events are pushed onto the
 * {@see EventQueueInterface} instead of being handled inline.
 */
interface QueueableEvent
{
    /**
     * Maximum number of dispatch attempts before the event is considered failed.
     */
    public function maxAttempts(): int;
}
