<?php

declare(strict_types=1);

namespace App\Core\Http\Controllers;

use App\Core\Kernel\KernelManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Exposes kernel + boot metrics for scraping (Prometheus/OpenTelemetry adapters
 * can be layered on top of this JSON payload).
 */
final class MetricsController
{
    public function __construct(private readonly KernelManager $kernel)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse([
            'kernel' => 'AxiomOS',
            'metrics' => $this->kernel->metrics(),
        ]);
    }
}
