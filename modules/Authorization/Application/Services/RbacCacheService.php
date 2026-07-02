<?php

declare(strict_types=1);

namespace Modules\Authorization\Application\Services;

final class RbacCacheService
{
    /** @var array<string, list<string>> */
    private static array $permissionCache = [];

    /** @var array<string, list<string>> */
    private static array $roleCache = [];

    /**
     * @param list<string> $permissions
     */
    public function rememberPermissions(string $cacheKey, array $permissions): void
    {
        self::$permissionCache[$cacheKey] = $permissions;
    }

    /**
     * @return list<string>|null
     */
    public function getPermissions(string $cacheKey): ?array
    {
        return self::$permissionCache[$cacheKey] ?? null;
    }

    /**
     * @param list<string> $roles
     */
    public function rememberRoles(string $cacheKey, array $roles): void
    {
        self::$roleCache[$cacheKey] = $roles;
    }

    /**
     * @return list<string>|null
     */
    public function getRoles(string $cacheKey): ?array
    {
        return self::$roleCache[$cacheKey] ?? null;
    }

    public function refreshForUser(string $assignableType, string $assignableId, ?string $organizationId = null): void
    {
        unset(self::$permissionCache[$this->key($assignableType, $assignableId, $organizationId)]);
        unset(self::$roleCache[$this->key($assignableType, $assignableId, $organizationId)]);
    }

    public function flush(): void
    {
        self::$permissionCache = [];
        self::$roleCache = [];
    }

    public function key(string $assignableType, string $assignableId, ?string $organizationId = null): string
    {
        return sprintf('%s:%s:%s', $assignableType, $assignableId, $organizationId ?? '*');
    }
}
