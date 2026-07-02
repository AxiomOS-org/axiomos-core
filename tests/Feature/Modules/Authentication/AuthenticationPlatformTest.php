<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Authentication;

use App\Core\Http\HttpKernel;
use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Modules\Authentication\Domain\Models\AuthCredential;
use Tests\Support\PostgresFeatureTestCase;

final class AuthenticationPlatformTest extends PostgresFeatureTestCase
{
    private HttpKernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernel = HttpKernelFactory::create($this->basePath);
    }

    public function test_authentication_endpoints(): void
    {
        $org = $this->decode(
            $this->kernel->handle(Request::create('/api/organizations?page=1&per_page=1', 'GET'))->getContent()
        )['data'][0];
        $identity = $this->decode(
            $this->kernel->handle(Request::create('/api/identities?page=1&per_page=1', 'GET'))->getContent()
        )['data'][0];

        $suffix = bin2hex(random_bytes(3));
        $email = 'auth.user.' . $suffix . '@axiomos.local';
        $password = 'AxiomOS@2026!';

        $userCreate = $this->kernel->handle(Request::create('/api/users', 'POST', [
            'identity_id' => $identity['id'],
            'organization_id' => $org['id'],
            'username' => 'auth.user.' . $suffix,
            'email' => $email,
            'display_name' => 'Auth User ' . $suffix,
            'status' => 'active',
        ]));
        self::assertSame(201, $userCreate->getStatusCode());
        $user = $this->decode($userCreate->getContent())['data'];

        AuthCredential::query()->create([
            'user_id' => $user['id'],
            'password_hash' => password_hash($password, PASSWORD_ARGON2ID),
            'status' => 'active',
        ]);

        $loginSuccess = $this->kernel->handle(Request::create('/api/auth/login', 'POST', [
            'email' => $email,
            'password' => $password,
            'ip_address' => '10.30.1.5',
            'user_agent' => 'AuthenticationPlatformTest',
        ]));
        self::assertSame(200, $loginSuccess->getStatusCode());
        $loginPayload = $this->decode($loginSuccess->getContent())['data'];
        self::assertArrayHasKey('session', $loginPayload);
        $sessionId = $loginPayload['session']['id'];

        $loginFailure = $this->kernel->handle(Request::create('/api/auth/login', 'POST', [
            'email' => $email,
            'password' => 'wrong-password',
        ]));
        self::assertSame(401, $loginFailure->getStatusCode());

        $sessions = $this->kernel->handle(Request::create('/api/auth/sessions?user_id=' . $user['id'], 'GET'));
        self::assertSame(200, $sessions->getStatusCode());
        self::assertNotEmpty($this->decode($sessions->getContent())['data']['sessions']);

        $logout = $this->kernel->handle(Request::create('/api/auth/logout', 'POST', ['session_id' => $sessionId]));
        self::assertSame(200, $logout->getStatusCode());

        $forgot = $this->kernel->handle(Request::create('/api/auth/password/forgot', 'POST', ['email' => $email]));
        self::assertSame(200, $forgot->getStatusCode());
        $resetToken = $this->decode($forgot->getContent())['data']['token'];
        self::assertIsString($resetToken);

        $reset = $this->kernel->handle(Request::create('/api/auth/password/reset', 'POST', [
            'token' => $resetToken,
            'new_password' => 'AxiomOS@2026!Reset',
        ]));
        self::assertSame(200, $reset->getStatusCode());

        for ($i = 0; $i < 6; $i++) {
            $this->kernel->handle(Request::create('/api/auth/login', 'POST', [
                'email' => 'blocked.' . $suffix . '@axiomos.local',
                'password' => 'invalid123',
            ]));
        }

        $rateLimited = $this->kernel->handle(Request::create('/api/auth/login', 'POST', [
            'email' => 'blocked.' . $suffix . '@axiomos.local',
            'password' => 'invalid123',
        ]));
        self::assertSame(401, $rateLimited->getStatusCode());
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
