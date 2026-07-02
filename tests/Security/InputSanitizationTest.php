<?php

declare(strict_types=1);

namespace Tests\Security;

use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Tests\Support\PostgresFeatureTestCase;

final class InputSanitizationTest extends PostgresFeatureTestCase
{
    public function test_xss_payload_in_search_query_does_not_execute_in_json_response(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);
        $payload = '<script>alert(1)</script>';
        $response = $kernel->handle(Request::create(
            '/api/users?search=' . rawurlencode($payload),
            'GET',
            server: ['HTTP_ACCEPT' => 'application/json'],
        ));

        self::assertSame(200, $response->getStatusCode());
        $content = (string) $response->getContent();
        self::assertStringNotContainsString('<script>', $content);
    }

    public function test_sql_injection_in_organization_search_does_not_return_500(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);
        $response = $kernel->handle(Request::create(
            "/api/organizations?search=' OR 1=1 --",
            'GET',
            server: ['HTTP_ACCEPT' => 'application/json'],
        ));

        self::assertLessThan(500, $response->getStatusCode());
    }
}
