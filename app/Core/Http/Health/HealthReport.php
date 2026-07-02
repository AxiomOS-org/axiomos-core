<?php

declare(strict_types=1);

namespace App\Core\Http\Health;

/**
 * Aggregated result of every health check.
 */
final readonly class HealthReport
{
    /**
     * @param list<HealthResult> $results
     */
    public function __construct(
        public HealthStatus $status,
        public array $results,
    ) {
    }

    public function isHealthy(): bool
    {
        return $this->status === HealthStatus::Ok;
    }

    /**
     * HTTP status code appropriate for the aggregate health.
     */
    public function httpStatus(): int
    {
        return $this->status === HealthStatus::Down ? 503 : 200;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
            'checks' => array_map(
                static fn (HealthResult $result): array => $result->toArray(),
                $this->results,
            ),
        ];
    }
}
