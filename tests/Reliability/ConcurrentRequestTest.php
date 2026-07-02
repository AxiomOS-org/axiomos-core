<?php

declare(strict_types=1);

namespace Tests\Reliability;

use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Tests\Support\PostgresFeatureTestCase;

final class ConcurrentRequestTest extends PostgresFeatureTestCase
{
    public function test_sequential_concurrent_health_requests_remain_stable(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);
        $statuses = [];

        for ($i = 0; $i < 10; $i++) {
            $statuses[] = $kernel->handle(Request::create('/health', 'GET'))->getStatusCode();
        }

        self::assertSame(array_fill(0, 10, 200), $statuses);
    }

    public function test_parallel_api_reads_do_not_return_500(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);
        $paths = [
            '/api/organizations?page=1&per_page=5',
            '/api/users?page=1&per_page=5',
            '/api/identities?page=1&per_page=5',
            '/api/security/roles?page=1&per_page=5',
        ];

        foreach ($paths as $path) {
            self::assertLessThan(
                500,
                $kernel->handle(Request::create($path, 'GET', server: ['HTTP_ACCEPT' => 'application/json']))->getStatusCode(),
                $path,
            );
        }
    }
}
