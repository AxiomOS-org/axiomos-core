<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Membership;

use App\Core\Http\HttpKernel;
use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Tests\Support\PostgresFeatureTestCase;

final class MembershipPlatformTest extends PostgresFeatureTestCase
{
    private HttpKernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernel = HttpKernelFactory::create($this->basePath);
    }

    public function test_membership_platform_endpoints_and_page(): void
    {
        $org = $this->decode(
            $this->kernel->handle(Request::create('/api/organizations?page=1&per_page=1', 'GET'))->getContent()
        )['data'][0];
        $identity = $this->decode(
            $this->kernel->handle(Request::create('/api/identities?page=1&per_page=1', 'GET'))->getContent()
        )['data'][0];

        $suffix = bin2hex(random_bytes(3));
        $userCreate = $this->kernel->handle(Request::create('/api/users', 'POST', [
            'identity_id' => $identity['id'],
            'username' => 'member.' . $suffix,
            'email' => 'member.' . $suffix . '@axiomos.local',
            'display_name' => 'Member ' . $suffix,
            'status' => 'active',
        ]));
        self::assertSame(201, $userCreate->getStatusCode());
        $user = $this->decode($userCreate->getContent())['data'];

        $paginated = $this->kernel->handle(Request::create('/api/memberships?page=1&per_page=5', 'GET'));
        self::assertSame(200, $paginated->getStatusCode());
        self::assertSame(5, $this->decode($paginated->getContent())['meta']['per_page']);

        $create = $this->kernel->handle(Request::create('/api/memberships', 'POST', [
            'user_id' => $user['id'],
            'organization_id' => $org['id'],
            'membership_type' => 'member',
            'status' => 'active',
            'scopes' => ['default' => true],
        ]));
        self::assertSame(201, $create->getStatusCode());
        $membership = $this->decode($create->getContent())['data'];
        self::assertSame($user['id'], $membership['user_id']);
        self::assertSame($org['id'], $membership['organization_id']);

        $show = $this->kernel->handle(Request::create('/api/memberships/' . $membership['id'], 'GET'));
        self::assertSame(200, $show->getStatusCode());
        self::assertSame($membership['id'], $this->decode($show->getContent())['data']['id']);

        $update = $this->kernel->handle(Request::create('/api/memberships/' . $membership['id'], 'PUT', [
            'membership_type' => 'admin',
        ]));
        self::assertSame(200, $update->getStatusCode());
        self::assertSame('admin', $this->decode($update->getContent())['data']['membership_type']);

        $delete = $this->kernel->handle(Request::create('/api/memberships/' . $membership['id'], 'DELETE'));
        self::assertSame(204, $delete->getStatusCode());

        $page = $this->kernel->handle(Request::create('/memberships', 'GET'));
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
