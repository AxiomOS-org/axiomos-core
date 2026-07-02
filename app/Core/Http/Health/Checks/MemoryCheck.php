<?php

declare(strict_types=1);

namespace App\Core\Http\Health\Checks;

use App\Core\Http\Health\HealthCheckInterface;
use App\Core\Http\Health\HealthResult;
use App\Core\Http\Health\HealthStatus;

/**
 * Flags the process as degraded once memory usage crosses a configured threshold.
 */
final class MemoryCheck implements HealthCheckInterface
{
    public function __construct(private readonly int $thresholdBytes = 256 * 1024 * 1024)
    {
    }

    public function name(): string
    {
        return 'memory';
    }

    public function run(): HealthResult
    {
        $usage = memory_get_usage(true);
        $degraded = $usage > $this->thresholdBytes;

        return new HealthResult(
            name: $this->name(),
            status: $degraded ? HealthStatus::Degraded : HealthStatus::Ok,
            message: sprintf('%.2f MB used.', $usage / 1_048_576),
            data: [
                'used_bytes' => $usage,
                'threshold_bytes' => $this->thresholdBytes,
            ],
        );
    }
}
