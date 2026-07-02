<?php

declare(strict_types=1);

namespace Modules\Organization\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Domain\Models\Organization;
use Modules\Organization\Domain\Repositories\Contracts\OrganizationRepositoryInterface;
use Modules\Organization\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentOrganizationRepository implements OrganizationRepositoryInterface
{
    use AppliesListQuery;

    public function all(): Collection
    {
        return Organization::query()->orderBy('name')->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = Organization::query();
        $this->applyListQuery($builder, $query, ['name', 'code', 'slug', 'description']);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?Organization
    {
        return Organization::query()->find($id);
    }

    public function create(array $attributes): Organization
    {
        return Organization::query()->create($attributes);
    }

    public function update(Organization $organization, array $attributes): Organization
    {
        $organization->fill($attributes);
        $organization->save();

        return $organization->refresh();
    }

    public function delete(Organization $organization): void
    {
        $organization->delete();
    }
}

