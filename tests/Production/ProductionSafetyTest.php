<?php

declare(strict_types=1);

namespace Tests\Production;

use App\Core\Http\HttpKernel;
use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Tests\Support\PostgresFeatureTestCase;

final class ProductionSafetyTest extends PostgresFeatureTestCase
{
    private HttpKernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernel = HttpKernelFactory::create($this->basePath);
    }

    public function test_health_endpoint_has_stable_production_shape(): void
    {
        $response = $this->kernel->handle(Request::create('/health', 'GET'));
        $content = $response->getContent();

        self::assertIsString($content);
        self::assertStringNotContainsString('stack trace', strtolower($content));
        self::assertStringNotContainsString('trace:', strtolower($content));

        /** @var array<string, mixed> $payload */
        $payload = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('status', $payload);
        self::assertArrayHasKey('checks', $payload);
    }

    public function test_user_listing_does_not_expose_password_hash(): void
    {
        $response = $this->kernel->handle(Request::create('/api/users?page=1&per_page=5', 'GET'));
        $content = $response->getContent();

        self::assertIsString($content);
        self::assertStringNotContainsString('password_hash', $content);
        self::assertStringNotContainsString('password_hash', strtolower($content));
    }

    public function test_internal_errors_do_not_leak_stack_traces(): void
    {
        $response = $this->kernel->handle(Request::create('/api/__stability-probe-invalid', 'GET', server: [
            'HTTP_ACCEPT' => 'application/json',
        ]));
        $content = strtolower((string) $response->getContent());

        self::assertStringNotContainsString('stack trace', $content);
        self::assertStringNotContainsString('#0 ', $content);
    }
}
