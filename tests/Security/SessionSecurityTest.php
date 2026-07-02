<?php

declare(strict_types=1);

namespace Tests\Security;

use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Modules\Authentication\Domain\Models\AuthCredential;
use Tests\Support\PostgresFeatureTestCase;

final class SessionSecurityTest extends PostgresFeatureTestCase
{
    public function test_logout_invalidates_session_token_lookup(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);

        $org = $this->decode($kernel->handle(Request::create('/api/organizations?page=1&per_page=1', 'GET'))->getContent())['data'][0];
        $identity = $this->decode($kernel->handle(Request::create('/api/identities?page=1&per_page=1', 'GET'))->getContent())['data'][0];

        $suffix = bin2hex(random_bytes(3));
        $email = 'session.sec.' . $suffix . '@axiomos.local';
        $password = 'AxiomOS@2026!';

        $user = $this->decode($kernel->handle(Request::create('/api/users', 'POST', [
            'identity_id' => $identity['id'],
            'organization_id' => $org['id'],
            'username' => 'session.sec.' . $suffix,
            'email' => $email,
            'display_name' => 'Session Security',
            'status' => 'active',
        ]))->getContent())['data'];

        AuthCredential::query()->create([
            'user_id' => $user['id'],
            'password_hash' => password_hash($password, PASSWORD_ARGON2ID),
            'status' => 'active',
        ]);

        $login = $this->decode($kernel->handle(Request::create('/api/auth/login', 'POST', [
            'email' => $email,
            'password' => $password,
        ]))->getContent())['data'];

        $sessionId = $login['session']['id'];
        self::assertSame(200, $kernel->handle(Request::create('/api/auth/logout', 'POST', ['session_id' => $sessionId]))->getStatusCode());

        $sessions = $this->decode($kernel->handle(Request::create('/api/auth/sessions?user_id=' . $user['id'], 'GET'))->getContent())['data']['sessions'];
        self::assertNotEmpty($sessions);
        self::assertSame('revoked', $sessions[0]['status'] ?? null);
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(string|false $content): array
    {
        self::assertIsString($content);

        /** @var array<string, mixed> */
        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }
}
