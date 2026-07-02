<?php

declare(strict_types=1);

namespace App\Core\Event;

use App\Core\Event\Contracts\EventQueueInterface;
use App\Core\Event\Support\EventEnvelope;

/**
 * In-memory FIFO queue ordered by availability time.
 *
 * Suitable for single-process runtimes and tests. A Redis/database backed
 * implementation can replace it without touching the bus (Dependency Inversion).
 */
final class InMemoryEventQueue implements EventQueueInterface
{
    /** @var list<EventEnvelope> */
    private array $envelopes = [];

    public function push(EventEnvelope $envelope): void
    {
        $this->envelopes[] = $envelope;
    }

    public function pop(float $now): ?EventEnvelope
    {
        $selectedIndex = null;
        $selectedAt = null;

        foreach ($this->envelopes as $index => $envelope) {
            if (! $envelope->isDue($now)) {
                continue;
            }

            if ($selectedAt === null || $envelope->availableAt < $selectedAt) {
                $selectedAt = $envelope->availableAt;
                $selectedIndex = $index;
            }
        }

        if ($selectedIndex === null) {
            return null;
        }

        $envelope = $this->envelopes[$selectedIndex];
        unset($this->envelopes[$selectedIndex]);
        $this->envelopes = array_values($this->envelopes);

        return $envelope;
    }

    public function size(): int
    {
        return count($this->envelopes);
    }
}
