<?php

declare(strict_types=1);

namespace Tests\Security;

use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Tests\Support\PostgresFeatureTestCase;

final class AuthorizationSecurityTest extends PostgresFeatureTestCase
{
    public function test_accessing_unknown_role_returns_client_error_not_500(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);
        $response = $kernel->handle(Request::create(
            '/api/security/roles/00000000-0000-4000-8000-000000009999',
            'GET',
            server: ['HTTP_ACCEPT' => 'application/json'],
        ));

        self::assertLessThan(500, $response->getStatusCode());
    }

    public function test_idor_user_endpoint_with_random_uuid_is_not_500(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);
        $response = $kernel->handle(Request::create(
            '/api/users/00000000-0000-4000-8000-000000009999',
            'GET',
            server: ['HTTP_ACCEPT' => 'application/json'],
        ));

        self::assertLessThan(500, $response->getStatusCode());
    }

    public function test_mass_assignment_payload_on_roles_is_validated_not_500(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);
        $response = $kernel->handle(Request::create('/api/security/roles', 'POST', [
            'slug' => '',
            'name' => '',
            'is_superadmin' => true,
        ], server: ['HTTP_ACCEPT' => 'application/json']));

        self::assertLessThan(500, $response->getStatusCode());
        self::assertGreaterThanOrEqual(400, $response->getStatusCode());
    }
}
