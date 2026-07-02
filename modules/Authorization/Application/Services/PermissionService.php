<?php

declare(strict_types=1);

namespace Modules\Authorization\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Authorization\Application\Support\ListQuery;
use Modules\Authorization\Domain\Models\AuthorizationPermission;
use RuntimeException;

final class PermissionService
{
    public function __construct(
        private readonly AuthorizationPlatformHooks $platform,
        private readonly RbacCacheService $cache,
    ) {
    }

    /** @return Collection<int, AuthorizationPermission> */
    public function list(): Collection
    {
        return AuthorizationPermission::query()->orderBy('module')->orderBy('action')->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return AuthorizationPermission::query()
            ->when($query->search !== null, static fn ($builder) => $builder->where(static function ($inner) use ($query): void {
                $inner->where('name', 'like', '%' . $query->search . '%')
                    ->orWhere('slug', 'like', '%' . $query->search . '%')
                    ->orWhere('module', 'like', '%' . $query->search . '%');
            }))
            ->when($query->status !== null, static fn ($builder) => $builder->where('status', $query->status))
            ->orderBy($query->sort, $query->direction)
            ->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function get(string $id): AuthorizationPermission
    {
        return AuthorizationPermission::query()->find($id)
            ?? throw new RuntimeException(sprintf('Permission "%s" not found.', $id));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): AuthorizationPermission
    {
        $permission = AuthorizationPermission::query()->create($attributes);
        $this->platform->onCreated($permission);
        $this->cache->flush();

        return $permission;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(string $id, array $attributes): AuthorizationPermission
    {
        $permission = $this->get($id);
        $before = $permission->toAuditSnapshot();
        $permission->fill($attributes);
        $permission->save();
        $permission = $permission->refresh();
        $this->platform->onUpdated($permission, $before);
        $this->cache->flush();

        return $permission;
    }

    public function delete(string $id): void
    {
        $permission = $this->get($id);
        $this->platform->onDeleted($permission);
        $permission->delete();
        $this->cache->flush();
    }
}
