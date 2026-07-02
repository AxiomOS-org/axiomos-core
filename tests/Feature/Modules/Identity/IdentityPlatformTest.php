<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Identity;

use App\Core\Http\HttpKernel;
use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Tests\Support\PostgresFeatureTestCase;

final class IdentityPlatformTest extends PostgresFeatureTestCase
{
    private HttpKernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernel = HttpKernelFactory::create($this->basePath);
    }

    public function test_identity_platform_endpoints_and_pages(): void
    {
        $org = $this->decode(
            $this->kernel->handle(Request::create('/api/organizations?page=1&per_page=1', 'GET'))->getContent()
        )['data'][0];

        $createIdentity = $this->kernel->handle(Request::create('/api/identities', 'POST', [
            'organization_id' => $org['id'],
            'identity_type' => 'employee',
            'code' => 'PH5B-ID-01',
            'display_name' => 'Phase 5B Identity',
            'status' => 'active',
        ]));
        self::assertSame(201, $createIdentity->getStatusCode());
        $identity = $this->decode($createIdentity->getContent())['data'];

        $teamCreate = $this->kernel->handle(Request::create('/api/teams', 'POST', [
            'organization_id' => $org['id'],
            'code' => 'PH5B-TEAM',
            'name' => 'Phase 5B Team',
            'leader_identity_id' => $identity['id'],
        ]));
        self::assertSame(201, $teamCreate->getStatusCode());
        $team = $this->decode($teamCreate->getContent())['data'];

        $memberCreate = $this->kernel->handle(Request::create('/api/team-members', 'POST', [
            'team_id' => $team['id'],
            'identity_id' => $identity['id'],
            'role' => 'member',
        ]));
        self::assertSame(201, $memberCreate->getStatusCode());
        $teamMember = $this->decode($memberCreate->getContent())['data'];

        $profileCreate = $this->kernel->handle(Request::create('/api/employee-profiles', 'POST', [
            'identity_id' => $identity['id'],
            'organization_id' => $org['id'],
            'employee_number' => 'PH5B-EMP-01',
            'job_title' => 'Platform Engineer',
            'status' => 'active',
        ]));
        self::assertSame(201, $profileCreate->getStatusCode());
        $profile = $this->decode($profileCreate->getContent())['data'];

        $contactCreate = $this->kernel->handle(Request::create('/api/contacts', 'POST', [
            'identity_id' => $identity['id'],
            'contact_type' => 'email',
            'value' => 'phase5b.identity@axiomos.local',
            'is_primary' => true,
            'status' => 'active',
        ]));
        self::assertSame(201, $contactCreate->getStatusCode());
        $contact = $this->decode($contactCreate->getContent())['data'];

        $deviceCreate = $this->kernel->handle(Request::create('/api/devices', 'POST', [
            'identity_id' => $identity['id'],
            'device_type' => 'workstation',
            'fingerprint' => hash('sha256', 'ph5b-device'),
            'user_agent' => 'AxiomOS Test Agent',
            'status' => 'active',
        ]));
        self::assertSame(201, $deviceCreate->getStatusCode());
        $device = $this->decode($deviceCreate->getContent())['data'];

        $sessionCreate = $this->kernel->handle(Request::create('/api/identity-sessions', 'POST', [
            'identity_id' => $identity['id'],
            'session_token_hash' => hash('sha256', 'ph5b-session'),
            'ip_address' => '10.20.1.10',
            'user_agent' => 'AxiomOS Test Session',
            'started_at' => Carbon::now()->toIso8601String(),
            'expires_at' => Carbon::now()->addDay()->toIso8601String(),
            'status' => 'active',
        ]));
        self::assertSame(201, $sessionCreate->getStatusCode());
        $session = $this->decode($sessionCreate->getContent())['data'];

        $suffix = bin2hex(random_bytes(3));
        $userCreate = $this->kernel->handle(Request::create('/api/users', 'POST', [
            'identity_id' => $identity['id'],
            'username' => 'identity.user.' . $suffix,
            'email' => 'identity.user.' . $suffix . '@axiomos.local',
            'display_name' => 'Identity User ' . $suffix,
            'status' => 'active',
        ]));
        self::assertSame(201, $userCreate->getStatusCode());
        $user = $this->decode($userCreate->getContent())['data'];

        $loginCreate = $this->kernel->handle(Request::create('/api/login-history', 'POST', [
            'identity_id' => $identity['id'],
            'user_id' => $user['id'],
            'ip_address' => '10.20.1.50',
            'user_agent' => 'AxiomOS Test Login',
            'success' => true,
            'logged_at' => Carbon::now()->toIso8601String(),
            'status' => 'success',
        ]));
        self::assertSame(201, $loginCreate->getStatusCode());
        $login = $this->decode($loginCreate->getContent())['data'];

        $tokenCreate = $this->kernel->handle(Request::create('/api/api-tokens', 'POST', [
            'identity_id' => $identity['id'],
            'name' => 'ph5b-test-token',
            'scopes' => ['identity:read'],
            'status' => 'active',
        ]));
        self::assertSame(201, $tokenCreate->getStatusCode());
        $tokenPayload = $this->decode($tokenCreate->getContent())['data'];
        self::assertArrayHasKey('plain_text_token', $tokenPayload);
        self::assertNotEmpty($tokenPayload['plain_text_token']);
        $token = $tokenPayload;

        $this->assertPaginatedIndex('/api/identities', 5);
        $this->assertPaginatedIndex('/api/teams', 5);
        $this->assertPaginatedIndex('/api/team-members', 5);
        $this->assertPaginatedIndex('/api/employee-profiles', 5);
        $this->assertPaginatedIndex('/api/contacts', 5);
        $this->assertPaginatedIndex('/api/devices', 5);
        $this->assertPaginatedIndex('/api/identity-sessions', 5);
        $this->assertPaginatedIndex('/api/login-history', 5);
        $this->assertPaginatedIndex('/api/api-tokens', 5);

        self::assertSame(200, $this->kernel->handle(Request::create('/api/identities/' . $identity['id'], 'GET'))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/api/teams/' . $team['id'], 'GET'))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/api/team-members/' . $teamMember['id'], 'GET'))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/api/employee-profiles/' . $profile['id'], 'GET'))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/api/contacts/' . $contact['id'], 'GET'))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/api/devices/' . $device['id'], 'GET'))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/api/identity-sessions/' . $session['id'], 'GET'))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/api/login-history/' . $login['id'], 'GET'))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/api/api-tokens/' . $token['id'], 'GET'))->getStatusCode());

        self::assertSame(200, $this->kernel->handle(Request::create('/api/contacts/' . $contact['id'], 'PUT', [
            'value' => 'updated.phase5b@axiomos.local',
        ]))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/api/devices/' . $device['id'], 'PUT', [
            'device_type' => 'mobile',
        ]))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/api/employee-profiles/' . $profile['id'], 'PUT', [
            'job_title' => 'Senior Platform Engineer',
        ]))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/api/api-tokens/' . $token['id'], 'PUT', [
            'name' => 'ph5b-updated-token',
        ]))->getStatusCode());

        self::assertSame(204, $this->kernel->handle(Request::create('/api/login-history/' . $login['id'], 'DELETE'))->getStatusCode());
        self::assertSame(204, $this->kernel->handle(Request::create('/api/api-tokens/' . $token['id'], 'DELETE'))->getStatusCode());
        self::assertSame(204, $this->kernel->handle(Request::create('/api/identity-sessions/' . $session['id'], 'DELETE'))->getStatusCode());
        self::assertSame(204, $this->kernel->handle(Request::create('/api/devices/' . $device['id'], 'DELETE'))->getStatusCode());
        self::assertSame(204, $this->kernel->handle(Request::create('/api/contacts/' . $contact['id'], 'DELETE'))->getStatusCode());
        self::assertSame(204, $this->kernel->handle(Request::create('/api/employee-profiles/' . $profile['id'], 'DELETE'))->getStatusCode());
        self::assertSame(204, $this->kernel->handle(Request::create('/api/team-members/' . $teamMember['id'], 'DELETE'))->getStatusCode());
        self::assertSame(204, $this->kernel->handle(Request::create('/api/teams/' . $team['id'], 'DELETE'))->getStatusCode());
        self::assertSame(204, $this->kernel->handle(Request::create('/api/identities/' . $identity['id'], 'DELETE'))->getStatusCode());

        self::assertSame(200, $this->kernel->handle(Request::create('/api/users?page=1&per_page=5', 'GET'))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/api/memberships?page=1&per_page=5', 'GET'))->getStatusCode());

        $dashboard = $this->kernel->handle(Request::create('/identity', 'GET'));
        self::assertSame(200, $dashboard->getStatusCode());
        self::assertStringContainsString('Identity Dashboard', (string) $dashboard->getContent());

        $identitiesPage = $this->kernel->handle(Request::create('/identity/identities', 'GET'));
        self::assertSame(200, $identitiesPage->getStatusCode());
        self::assertStringContainsString('Identities', (string) $identitiesPage->getContent());

        self::assertSame(200, $this->kernel->handle(Request::create('/users', 'GET'))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/memberships', 'GET'))->getStatusCode());
    }

    private function assertPaginatedIndex(string $path, int $perPage): void
    {
        $response = $this->kernel->handle(Request::create($path . '?page=1&per_page=' . $perPage, 'GET'));
        self::assertSame(200, $response->getStatusCode());
        self::assertSame($perPage, $this->decode($response->getContent())['meta']['per_page']);
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
