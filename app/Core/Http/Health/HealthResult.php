<?php

declare(strict_types=1);

namespace App\Core\Http\Health;

/**
 * Outcome of a single health check.
 */
final readonly class HealthResult
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public string $name,
        public HealthStatus $status,
        public string $message = '',
        public array $data = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'status' => $this->status->value,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}
