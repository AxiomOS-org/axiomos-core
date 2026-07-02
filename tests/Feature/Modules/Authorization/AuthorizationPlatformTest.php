<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Authorization;

use App\Core\Http\HttpKernel;
use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\Support\PostgresFeatureTestCase;

final class AuthorizationPlatformTest extends PostgresFeatureTestCase
{
    private HttpKernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->purgeModuleManifestCache();
        $this->kernel = HttpKernelFactory::create($this->basePath);
    }

    public function test_roles_permissions_assignment_and_user_access_queries(): void
    {
        $suffix = bin2hex(random_bytes(3));
        $userId = (string) Str::uuid();

        $permissionCreate = $this->kernel->handle(Request::create('/api/security/permissions', 'POST', [
            'slug' => 'security.audit.view.' . $suffix,
            'name' => 'Security Audit View ' . $suffix,
            'module' => 'security',
            'action' => 'audit.view',
            'status' => 'active',
        ]));
        self::assertSame(201, $permissionCreate->getStatusCode(), (string) $permissionCreate->getContent());
        $permission = $this->decode($permissionCreate->getContent())['data'];

        $roleCreate = $this->kernel->handle(Request::create('/api/security/roles', 'POST', [
            'slug' => 'auditor-' . $suffix,
            'name' => 'Auditor ' . $suffix,
            'description' => 'Security auditor role',
            'status' => 'active',
            'permission_ids' => [$permission['id']],
        ]));
        self::assertSame(201, $roleCreate->getStatusCode());
        $role = $this->decode($roleCreate->getContent())['data'];

        $roleIndex = $this->kernel->handle(Request::create('/api/security/roles?page=1&per_page=5', 'GET'));
        self::assertSame(200, $roleIndex->getStatusCode());
        self::assertSame(5, $this->decode($roleIndex->getContent())['meta']['per_page']);

        $assign = $this->kernel->handle(Request::create('/api/security/roles/' . $role['id'] . '/assign', 'POST', [
            'assignable_id' => $userId,
            'assignable_type' => 'Modules\\Users\\Domain\\Models\\User',
        ]));
        self::assertSame(201, $assign->getStatusCode());

        $userPermissions = $this->kernel->handle(Request::create('/api/security/users/' . $userId . '/permissions', 'GET'));
        self::assertSame(200, $userPermissions->getStatusCode());
        self::assertContains($permission['slug'], $this->decode($userPermissions->getContent())['data']);

        $userRoles = $this->kernel->handle(Request::create('/api/security/users/' . $userId . '/roles', 'GET'));
        self::assertSame(200, $userRoles->getStatusCode());
        self::assertContains($role['slug'], $this->decode($userRoles->getContent())['data']);

        $roleUpdate = $this->kernel->handle(Request::create('/api/security/roles/' . $role['id'], 'PUT', [
            'name' => 'Auditor Updated ' . $suffix,
        ]));
        self::assertSame(200, $roleUpdate->getStatusCode());

        $revoke = $this->kernel->handle(Request::create('/api/security/roles/' . $role['id'] . '/revoke', 'POST', [
            'assignable_id' => $userId,
            'assignable_type' => 'Modules\\Users\\Domain\\Models\\User',
        ]));
        self::assertSame(200, $revoke->getStatusCode());

        self::assertSame(200, $this->kernel->handle(Request::create('/security/dashboard', 'GET'))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/security/roles', 'GET'))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/security/permissions', 'GET'))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/security/sessions', 'GET'))->getStatusCode());
        self::assertSame(200, $this->kernel->handle(Request::create('/security/login-history', 'GET'))->getStatusCode());

        self::assertSame(204, $this->kernel->handle(Request::create('/api/security/roles/' . $role['id'], 'DELETE'))->getStatusCode());
        self::assertSame(204, $this->kernel->handle(Request::create('/api/security/permissions/' . $permission['id'], 'DELETE'))->getStatusCode());
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
