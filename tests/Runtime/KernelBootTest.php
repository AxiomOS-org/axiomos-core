<?php

declare(strict_types=1);

namespace Tests\Runtime;

use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Tests\Support\PostgresFeatureTestCase;

final class KernelBootTest extends PostgresFeatureTestCase
{
    public function test_kernel_boots_without_fatal_error(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);
        $response = $kernel->handle(Request::create('/health', 'GET'));

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('ready', strtolower((string) $response->getContent()));
    }

    public function test_all_modules_are_booted(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);
        $kernel->handle(Request::create('/health', 'GET'));

        $response = $kernel->handle(Request::create('/metrics', 'GET'));
        $content = $response->getContent();
        self::assertIsString($content);

        /** @var array<string, mixed> $payload */
        $payload = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        $loadedModules = $payload['metrics']['loadedModules'] ?? [];
        self::assertIsArray($loadedModules);
        self::assertGreaterThanOrEqual(20, count($loadedModules));
    }
}
