<?php

declare(strict_types=1);

namespace App\Core\Http\Health\Checks;

use App\Core\Http\Health\HealthCheckInterface;
use App\Core\Http\Health\HealthResult;
use App\Core\Http\Health\HealthStatus;
use App\Core\Kernel\Contracts\KernelInterface;

/**
 * Reports on module boot outcome: degraded when any module failed, down when
 * nothing booted, ok otherwise.
 */
final class ModulesBootedCheck implements HealthCheckInterface
{
    public function __construct(private readonly KernelInterface $kernel)
    {
    }

    public function name(): string
    {
        return 'modules';
    }

    public function run(): HealthResult
    {
        $report = $this->kernel->lastBootReport();

        if ($report === null) {
            return new HealthResult(
                name: $this->name(),
                status: HealthStatus::Down,
                message: 'Kernel has not booted any modules yet.',
            );
        }

        $loaded = $report->metrics->loadedModulesCount;
        $failed = $report->metrics->failedModulesCount;

        $status = match (true) {
            $failed > 0 => HealthStatus::Degraded,
            $loaded === 0 => HealthStatus::Down,
            default => HealthStatus::Ok,
        };

        return new HealthResult(
            name: $this->name(),
            status: $status,
            message: sprintf('%d loaded, %d failed, %d skipped.', $loaded, $failed, $report->metrics->skippedModulesCount),
            data: [
                'loaded' => $loaded,
                'failed' => $failed,
                'skipped' => $report->metrics->skippedModulesCount,
            ],
        );
    }
}
