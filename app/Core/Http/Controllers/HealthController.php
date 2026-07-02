<?php

declare(strict_types=1);

namespace App\Core\Http\Controllers;

use App\Core\Http\Health\HealthChecker;
use App\Core\Kernel\KernelManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Liveness/readiness endpoint returning the canonical AxiomOS health payload
 * plus the detailed health-check breakdown for observability tooling.
 */
final class HealthController
{
    public function __construct(
        private readonly KernelManager $kernel,
        private readonly HealthChecker $health,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $status = $this->kernel->status();
        $health = $this->kernel->health();
        $report = $this->health->run();

        return new JsonResponse([
            'kernel' => 'AxiomOS',
            'status' => $status['status'],
            'version' => $status['version'],
            'modules' => $status['modules'],
            'bootTime' => $status['bootTime'],
            'memory' => sprintf('%.2f MB', $health['memoryUsage'] / 1_048_576),
            'checks' => $report->toArray()['checks'],
        ], $report->httpStatus());
    }
}
