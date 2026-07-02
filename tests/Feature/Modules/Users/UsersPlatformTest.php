<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Users;

use App\Core\Http\HttpKernel;
use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Tests\Support\PostgresFeatureTestCase;

final class UsersPlatformTest extends PostgresFeatureTestCase
{
    private HttpKernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernel = HttpKernelFactory::create($this->basePath);
    }

    public function test_users_platform_endpoints_and_page(): void
    {
        $identity = $this->decode(
            $this->kernel->handle(Request::create('/api/identities?page=1&per_page=1', 'GET'))->getContent()
        )['data'][0];

        $paginated = $this->kernel->handle(Request::create('/api/users?page=1&per_page=5', 'GET'));
        self::assertSame(200, $paginated->getStatusCode());
        self::assertSame(5, $this->decode($paginated->getContent())['meta']['per_page']);

        $suffix = bin2hex(random_bytes(3));
        $create = $this->kernel->handle(Request::create('/api/users', 'POST', [
            'identity_id' => $identity['id'],
            'username' => 'user.' . $suffix,
            'email' => 'user.' . $suffix . '@axiomos.local',
            'display_name' => 'User ' . $suffix,
            'status' => 'active',
        ]));
        self::assertSame(201, $create->getStatusCode());
        $user = $this->decode($create->getContent())['data'];
        self::assertSame($identity['id'], $user['identity_id']);

        $show = $this->kernel->handle(Request::create('/api/users/' . $user['id'], 'GET'));
        self::assertSame(200, $show->getStatusCode());
        self::assertSame($user['id'], $this->decode($show->getContent())['data']['id']);

        $update = $this->kernel->handle(Request::create('/api/users/' . $user['id'], 'PUT', [
            'display_name' => 'Updated User ' . $suffix,
        ]));
        self::assertSame(200, $update->getStatusCode());
        self::assertSame('Updated User ' . $suffix, $this->decode($update->getContent())['data']['display_name']);

        $delete = $this->kernel->handle(Request::create('/api/users/' . $user['id'], 'DELETE'));
        self::assertSame(204, $delete->getStatusCode());

        $page = $this->kernel->handle(Request::create('/users', 'GET'));
        self::assertSame(200, $page->getStatusCode());
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(string|false $content): array
    {
        self::assertIsString($content);
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }
}
