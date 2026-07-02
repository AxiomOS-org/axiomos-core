<?php

declare(strict_types=1);

namespace Modules\Authorization\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Authorization\Application\Support\ListQuery;
use Modules\Authorization\Domain\Models\AuthorizationRole;
use Modules\Authorization\Domain\Models\AuthorizationRolePermission;
use RuntimeException;

final class RoleService
{
    public function __construct(
        private readonly AuthorizationPlatformHooks $platform,
        private readonly RbacCacheService $cache,
    ) {
    }

    /** @return Collection<int, AuthorizationRole> */
    public function list(?string $organizationId = null): Collection
    {
        return AuthorizationRole::query()
            ->when($organizationId !== null, static fn ($query) => $query->where('organization_id', $organizationId))
            ->orderByDesc('created_at')
            ->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return AuthorizationRole::query()
            ->when($query->organizationId !== null, static fn ($builder) => $builder->where('organization_id', $query->organizationId))
            ->when($query->search !== null, static fn ($builder) => $builder->where(static function ($inner) use ($query): void {
                $inner->where('name', 'like', '%' . $query->search . '%')
                    ->orWhere('slug', 'like', '%' . $query->search . '%');
            }))
            ->when($query->status !== null, static fn ($builder) => $builder->where('status', $query->status))
            ->orderBy($query->sort, $query->direction)
            ->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function get(string $id): AuthorizationRole
    {
        return AuthorizationRole::query()->find($id)
            ?? throw new RuntimeException(sprintf('Role "%s" not found.', $id));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): AuthorizationRole
    {
        $role = AuthorizationRole::query()->create($attributes);
        $this->platform->onCreated($role);
        $this->cache->flush();

        return $role;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(string $id, array $attributes): AuthorizationRole
    {
        $role = $this->get($id);
        $before = $role->toAuditSnapshot();
        $role->fill($attributes);
        $role->save();
        $role = $role->refresh();
        $this->platform->onUpdated($role, $before);
        $this->cache->flush();

        return $role;
    }

    public function delete(string $id): void
    {
        $role = $this->get($id);
        $this->platform->onDeleted($role);
        $role->delete();
        $this->cache->flush();
    }

    /**
     * @param list<string> $permissionIds
     */
    public function syncPermissions(string $roleId, array $permissionIds): AuthorizationRole
    {
        $role = $this->get($roleId);
        AuthorizationRolePermission::query()
            ->where('role_id', $role->id)
            ->whereNotIn('permission_id', $permissionIds)
            ->delete();

        foreach (array_values(array_unique($permissionIds)) as $permissionId) {
            AuthorizationRolePermission::query()->firstOrCreate([
                'role_id' => $role->id,
                'permission_id' => $permissionId,
            ]);
        }

        $this->cache->flush();

        return $role->refresh();
    }
}
