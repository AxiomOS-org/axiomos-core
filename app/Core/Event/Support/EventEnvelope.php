<?php

declare(strict_types=1);

namespace App\Core\Event\Support;

/**
 * Wraps a queued event with delivery metadata (attempts, availability).
 */
final readonly class EventEnvelope
{
    public function __construct(
        public object $event,
        public int $attempts,
        public int $maxAttempts,
        public float $availableAt,
    ) {
    }

    public function isDue(float $now): bool
    {
        return $this->availableAt <= $now;
    }

    public function canRetry(): bool
    {
        return $this->attempts < $this->maxAttempts;
    }

    public function nextAttempt(float $availableAt): self
    {
        return new self(
            event: $this->event,
            attempts: $this->attempts + 1,
            maxAttempts: $this->maxAttempts,
            availableAt: $availableAt,
        );
    }
}
