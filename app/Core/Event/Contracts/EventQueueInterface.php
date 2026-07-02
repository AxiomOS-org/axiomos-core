<?php

declare(strict_types=1);

namespace App\Core\Event\Contracts;

use App\Core\Event\Support\EventEnvelope;

/**
 * Backing store for queued, async and delayed events.
 */
interface EventQueueInterface
{
    public function push(EventEnvelope $envelope): void;

    /**
     * Pop the next envelope that is due at the given timestamp, or null if none.
     */
    public function pop(float $now): ?EventEnvelope;

    /**
     * Number of envelopes currently waiting in the queue (due or not).
     */
    public function size(): int;
}
