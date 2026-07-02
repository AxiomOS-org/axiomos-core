<?php

declare(strict_types=1);

namespace Tests\QA;

use Tests\Support\QA\RouteMatrixProbe;

final class ApiContractTest extends RouteMatrixProbe
{
    public function test_health_contract_snapshot(): void
    {
        $payload = $this->healthContract();

        self::assertArrayHasKey('kernel', $payload);
        self::assertArrayHasKey('status', $payload);
        self::assertArrayHasKey('version', $payload);
        self::assertArrayHasKey('modules', $payload);
        self::assertArrayHasKey('checks', $payload);
        self::assertSame('AxiomOS', $payload['kernel']);
    }

    public function test_metrics_contract_has_kernel_metrics(): void
    {
        $content = $this->kernel->handle(
            \Illuminate\Http\Request::create('/metrics', 'GET'),
        )->getContent();

        self::assertIsString($content);
        /** @var array<string, mixed> $payload */
        $payload = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('metrics', $payload);
        self::assertArrayHasKey('loadedModules', $payload['metrics']);
    }
}
