<?php

declare(strict_types=1);

namespace Tests\Security;

use App\Core\Http\HttpKernel;
use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Modules\Authorization\Application\Services\LoginSecurityService;
use Tests\Support\PostgresFeatureTestCase;

final class AuthenticationSecurityTest extends PostgresFeatureTestCase
{
    private HttpKernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->purgeModuleManifestCache();
        $this->kernel = HttpKernelFactory::create($this->basePath);
    }

    public function test_password_is_not_returned_in_api_responses(): void
    {
        $suffix = bin2hex(random_bytes(3));
        $create = $this->kernel->handle(Request::create('/api/security/roles', 'POST', [
            'slug' => 'security-redaction-' . $suffix,
            'name' => 'Security Redaction ' . $suffix,
            'status' => 'active',
        ]));
        self::assertSame(201, $create->getStatusCode(), (string) $create->getContent());
        $role = $this->decode($create->getContent())['data'];

        self::assertArrayNotHasKey('password', $role);
        self::assertArrayNotHasKey('password_hash', $role);

        $show = $this->kernel->handle(Request::create('/api/security/roles/' . $role['id'], 'GET'));
        self::assertSame(200, $show->getStatusCode());
        $showPayload = $this->decode($show->getContent())['data'];
        self::assertArrayNotHasKey('password', $showPayload);
        self::assertArrayNotHasKey('password_hash', $showPayload);

        self::assertSame(204, $this->kernel->handle(Request::create('/api/security/roles/' . $role['id'], 'DELETE'))->getStatusCode());
    }

    public function test_rate_limiting_blocks_brute_force_attempts(): void
    {
        $guard = new LoginSecurityService();
        $email = 'attacker@axiomos.local';
        $guard->reset($email);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $guard->registerFailedAttempt($email);
        }

        self::assertTrue($guard->isRateLimited($email, 5));
    }

    public function test_sql_injection_payload_in_login_email_is_sanitized(): void
    {
        $guard = new LoginSecurityService();
        $payload = "admin' OR 1=1 -- @axiomos.local";
        $sanitized = $guard->sanitizeEmail($payload);

        self::assertStringNotContainsString("'", $sanitized);
        self::assertStringNotContainsString(' ', $sanitized);
        self::assertStringNotContainsString('--', $sanitized);
        self::assertStringContainsString('@axiomos.local', $sanitized);
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

    private function purgeModuleManifestCache(): void
    {
        $cachePath = $this->basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache';

        if (! is_dir($cachePath)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($cachePath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                @rmdir($item->getPathname());
                continue;
            }

            if ($item->getFilename() === '.gitignore') {
                continue;
            }

            @unlink($item->getPathname());
        }
    }
}
