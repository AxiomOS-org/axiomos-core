<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Http;

use App\Core\Http\HttpKernel;
use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Tests\Support\PostgresFeatureTestCase;

/**
 * End-to-end HTTP integration against the real modules directory.
 *
 * Boots the AxiomOS kernel through the Laravel router and asserts the milestone
 * contract: a ready kernel serving `/` and `/health` with four core modules.
 */
final class HttpKernelTest extends PostgresFeatureTestCase
{
    private HttpKernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kernel = HttpKernelFactory::create($this->basePath);
    }

    public function test_root_returns_kernel_booted_message(): void
    {
        $response = $this->kernel->handle(Request::create('/', 'GET'));

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('AxiomOS Kernel Booted Successfully', $response->getContent());
    }

    public function test_health_returns_canonical_payload(): void
    {
        $response = $this->kernel->handle(Request::create('/health', 'GET'));

        self::assertSame(200, $response->getStatusCode());

        $payload = $this->json($response->getContent());

        self::assertSame('AxiomOS', $payload['kernel']);
        self::assertSame('ready', $payload['status']);
        self::assertSame('1.0.0', $payload['version']);
        self::assertSame(5, $payload['modules']);
        self::assertMatchesRegularExpression('/^\d+\.\d{2} ms$/', $payload['bootTime']);
        self::assertMatchesRegularExpression('/^\d+\.\d{2} MB$/', $payload['memory']);
    }

    public function test_health_includes_check_breakdown(): void
    {
        $response = $this->kernel->handle(Request::create('/health', 'GET'));
        $payload = $this->json($response->getContent());

        $names = array_column($payload['checks'], 'name');

        self::assertContains('kernel', $names);
        self::assertContains('modules', $names);
        self::assertContains('memory', $names);
        self::assertContains('platform', $names);

        foreach ($payload['checks'] as $check) {
            self::assertSame('ok', $check['status']);
        }
    }

    public function test_metrics_endpoint_reports_loaded_modules(): void
    {
        $response = $this->kernel->handle(Request::create('/metrics', 'GET'));

        self::assertSame(200, $response->getStatusCode());

        $payload = $this->json($response->getContent());

        self::assertSame('AxiomOS', $payload['kernel']);
        self::assertCount(5, $payload['metrics']['loadedModules']);
        self::assertSame(1, $payload['metrics']['bootCount']);
    }

    public function test_unknown_route_returns_404(): void
    {
        $response = $this->kernel->handle(Request::create('/does-not-exist', 'GET'));

        self::assertSame(404, $response->getStatusCode());

        $payload = $this->json($response->getContent());
        self::assertSame('not_found', $payload['status']);
    }

    public function test_kernel_boots_once_and_is_reused_across_requests(): void
    {
        $first = $this->kernel->handle(Request::create('/health', 'GET'));
        $second = $this->kernel->handle(Request::create('/metrics', 'GET'));

        self::assertSame(200, $first->getStatusCode());
        self::assertSame(200, $second->getStatusCode());

        $payload = $this->json($second->getContent());
        self::assertSame(1, $payload['metrics']['bootCount']);
    }

    /**
     * @return array<string, mixed>
     */
    private function json(string|false $content): array
    {
        self::assertIsString($content);

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }
}
