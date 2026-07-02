<?php

declare(strict_types=1);

namespace Modules\Authorization\Application\Services;

use Modules\Authorization\Domain\Models\AuthorizationRole;
use Modules\Authorization\Domain\Models\AuthorizationRoleAssignment;

final class AuthorizationService
{
    public function __construct(private readonly RbacCacheService $cache)
    {
    }

    public function assignRole(string $roleId, string $assignableType, string $assignableId, ?string $organizationId = null): AuthorizationRoleAssignment
    {
        $assignment = AuthorizationRoleAssignment::query()->firstOrCreate([
            'role_id' => $roleId,
            'assignable_type' => $assignableType,
            'assignable_id' => $assignableId,
            'organization_id' => $organizationId,
        ]);

        $this->cache->refreshForUser($assignableType, $assignableId, $organizationId);

        return $assignment;
    }

    public function revokeRole(string $roleId, string $assignableType, string $assignableId, ?string $organizationId = null): void
    {
        AuthorizationRoleAssignment::query()
            ->where('role_id', $roleId)
            ->where('assignable_type', $assignableType)
            ->where('assignable_id', $assignableId)
            ->where('organization_id', $organizationId)
            ->delete();

        $this->cache->refreshForUser($assignableType, $assignableId, $organizationId);
    }

    public function userHasPermission(string $userId, string $permissionSlug, ?string $organizationId = null): bool
    {
        return in_array($permissionSlug, $this->getUserPermissions($userId, $organizationId), true);
    }

    public function userHasRole(string $userId, string $roleSlug, ?string $organizationId = null): bool
    {
        return in_array($roleSlug, $this->getUserRoles($userId, $organizationId), true);
    }

    /**
     * @return list<string>
     */
    public function getUserPermissions(string $userId, ?string $organizationId = null): array
    {
        $cacheKey = $this->cache->key('Modules\\Users\\Domain\\Models\\User', $userId, $organizationId);
        $cached = $this->cache->getPermissions($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        $permissions = AuthorizationRoleAssignment::query()
            ->with(['role.permissions'])
            ->where('assignable_type', 'Modules\\Users\\Domain\\Models\\User')
            ->where('assignable_id', $userId)
            ->when($organizationId !== null, static fn ($query) => $query->where('organization_id', $organizationId))
            ->get()
            ->flatMap(static fn (AuthorizationRoleAssignment $assignment) => $assignment->role?->permissions->pluck('slug') ?? [])
            ->filter(static fn ($slug): bool => is_string($slug) && $slug !== '')
            ->unique()
            ->values()
            ->all();

        /** @var list<string> $permissions */
        $permissions = $permissions;
        $this->cache->rememberPermissions($cacheKey, $permissions);

        return $permissions;
    }

    /**
     * @return list<string>
     */
    public function getUserRoles(string $userId, ?string $organizationId = null): array
    {
        $cacheKey = $this->cache->key('Modules\\Users\\Domain\\Models\\User', $userId, $organizationId);
        $cached = $this->cache->getRoles($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        $roles = AuthorizationRoleAssignment::query()
            ->with('role')
            ->where('assignable_type', 'Modules\\Users\\Domain\\Models\\User')
            ->where('assignable_id', $userId)
            ->when($organizationId !== null, static fn ($query) => $query->where('organization_id', $organizationId))
            ->get()
            ->map(static fn (AuthorizationRoleAssignment $assignment): ?string => $assignment->role?->slug)
            ->filter(static fn ($slug): bool => is_string($slug) && $slug !== '')
            ->unique()
            ->values()
            ->all();

        /** @var list<string> $roles */
        $roles = $roles;
        $this->cache->rememberRoles($cacheKey, $roles);

        return $roles;
    }
}
