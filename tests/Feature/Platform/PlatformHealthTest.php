<?php

declare(strict_types=1);

namespace Tests\Feature\Platform;

use App\Core\Http\HttpKernel;
use App\Core\Http\HttpKernelFactory;
use App\Platform\Support\PlatformCapabilities;
use Illuminate\Http\Request;
use Tests\Support\PostgresFeatureTestCase;

final class PlatformHealthTest extends PostgresFeatureTestCase
{
    private HttpKernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kernel = HttpKernelFactory::create($this->basePath);
    }

    public function test_health_includes_platform_check(): void
    {
        $response = $this->kernel->handle(Request::create('/health', 'GET'));

        self::assertSame(200, $response->getStatusCode());

        $payload = $this->json($response->getContent());
        $names = array_column($payload['checks'], 'name');

        self::assertContains('platform', $names);

        $platform = array_values(array_filter(
            $payload['checks'],
            static fn (array $check): bool => $check['name'] === 'platform',
        ))[0];

        self::assertSame('ok', $platform['status']);
        self::assertSame(PlatformCapabilities::count(), $platform['data']['capability_count']);
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

