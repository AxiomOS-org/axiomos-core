<?php

declare(strict_types=1);

namespace Tests\Reliability;

use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Tests\Support\PostgresFeatureTestCase;

final class FailureSimulationTest extends PostgresFeatureTestCase
{
    public function test_kernel_survives_missing_queue_driver_gracefully(): void
    {
        putenv('QUEUE_CONNECTION=missing-driver');
        $_ENV['QUEUE_CONNECTION'] = 'missing-driver';

        $kernel = HttpKernelFactory::create($this->basePath);
        $response = $kernel->handle(Request::create('/health', 'GET'));

        self::assertSame(200, $response->getStatusCode());
    }

    public function test_kernel_survives_cache_driver_fallback(): void
    {
        putenv('CACHE_DRIVER=array');
        $_ENV['CACHE_DRIVER'] = 'array';

        $kernel = HttpKernelFactory::create($this->basePath);
        $response = $kernel->handle(Request::create('/metrics', 'GET'));

        self::assertSame(200, $response->getStatusCode());
    }

    public function test_sample_plugin_failure_does_not_break_health(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);
        $response = $kernel->handle(Request::create('/health', 'GET'));

        self::assertSame(200, $response->getStatusCode());
        self::assertStringNotContainsString('Plugin', (string) $response->getContent());
    }
}
