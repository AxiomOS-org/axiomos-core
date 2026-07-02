<?php

declare(strict_types=1);

namespace App\Core\Event\Support;

/**
 * Mutable, in-process counters for event bus observability.
 *
 * Designed to be snapshotted and exported to Prometheus/OpenTelemetry.
 */
final class EventMetrics
{
    private int $dispatched = 0;

    private int $failed = 0;

    private int $queued = 0;

    private int $retried = 0;

    private int $listenerInvocations = 0;

    private float $totalDurationMs = 0.0;

    /** @var array<string, int> */
    private array $perEvent = [];

    public function recordDispatch(string $eventName, int $listeners, float $durationMs): void
    {
        $this->dispatched++;
        $this->listenerInvocations += $listeners;
        $this->totalDurationMs += $durationMs;
        $this->perEvent[$eventName] = ($this->perEvent[$eventName] ?? 0) + 1;
    }

    public function recordFailure(): void
    {
        $this->failed++;
    }

    public function recordQueued(): void
    {
        $this->queued++;
    }

    public function recordRetry(): void
    {
        $this->retried++;
    }

    public function averageDurationMs(): float
    {
        return $this->dispatched === 0 ? 0.0 : $this->totalDurationMs / $this->dispatched;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'dispatched' => $this->dispatched,
            'failed' => $this->failed,
            'queued' => $this->queued,
            'retried' => $this->retried,
            'listener_invocations' => $this->listenerInvocations,
            'total_duration_ms' => $this->totalDurationMs,
            'average_duration_ms' => $this->averageDurationMs(),
            'per_event' => $this->perEvent,
        ];
    }

    public function dispatched(): int
    {
        return $this->dispatched;
    }

    public function failed(): int
    {
        return $this->failed;
    }

    public function queued(): int
    {
        return $this->queued;
    }

    public function retried(): int
    {
        return $this->retried;
    }
}
