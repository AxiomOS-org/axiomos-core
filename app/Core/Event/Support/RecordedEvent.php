<?php

declare(strict_types=1);

namespace App\Core\Event\Support;

/**
 * Immutable history record for a dispatched event.
 */
final readonly class RecordedEvent
{
    public function __construct(
        public string $eventName,
        public int $listenerCount,
        public float $durationMs,
        public bool $succeeded,
        public float $occurredAt,
        public ?string $error = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'event' => $this->eventName,
            'listeners' => $this->listenerCount,
            'duration_ms' => $this->durationMs,
            'succeeded' => $this->succeeded,
            'occurred_at' => $this->occurredAt,
            'error' => $this->error,
        ];
    }
}
