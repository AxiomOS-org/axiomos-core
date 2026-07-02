<?php

declare(strict_types=1);

namespace Modules\Identity\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\Identity;
use Modules\Identity\Domain\Repositories\Contracts\IdentityRepositoryInterface;
use Modules\Identity\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentIdentityRepository implements IdentityRepositoryInterface
{
    use AppliesListQuery;

    public function all(?string $organizationId = null): Collection
    {
        $query = Identity::query()->orderBy('display_name');

        if ($organizationId !== null) {
            $query->where('organization_id', $organizationId);
        }

        return $query->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = Identity::query();

        if ($query->organizationId !== null) {
            $builder->where('organization_id', $query->organizationId);
        }

        $this->applyListQuery($builder, $query, ['display_name', 'code', 'email', 'phone']);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?Identity
    {
        return Identity::query()->find($id);
    }

    public function create(array $attributes): Identity
    {
        return Identity::query()->create($attributes);
    }

    public function update(Identity $identity, array $attributes): Identity
    {
        $identity->fill($attributes);
        $identity->save();

        return $identity->refresh();
    }

    public function delete(Identity $identity): void
    {
        $identity->delete();
    }
}
