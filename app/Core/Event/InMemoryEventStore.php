<?php

declare(strict_types=1);

namespace App\Core\Event;

use App\Core\Event\Contracts\EventStoreInterface;
use App\Core\Event\Support\RecordedEvent;

/**
 * Bounded in-memory event history (ring buffer semantics).
 */
final class InMemoryEventStore implements EventStoreInterface
{
    /** @var list<RecordedEvent> */
    private array $events = [];

    public function __construct(private readonly int $maxSize = 1000)
    {
    }

    public function record(RecordedEvent $event): void
    {
        $this->events[] = $event;

        if (count($this->events) > $this->maxSize) {
            array_shift($this->events);
        }
    }

    public function all(): array
    {
        return $this->events;
    }

    public function clear(): void
    {
        $this->events = [];
    }
}
