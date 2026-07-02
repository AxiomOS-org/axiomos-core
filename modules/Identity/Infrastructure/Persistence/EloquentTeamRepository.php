<?php

declare(strict_types=1);

namespace Modules\Identity\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\Team;
use Modules\Identity\Domain\Repositories\Contracts\TeamRepositoryInterface;
use Modules\Identity\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentTeamRepository implements TeamRepositoryInterface
{
    use AppliesListQuery;

    public function all(?string $organizationId = null): Collection
    {
        $query = Team::query()->with('leader')->orderBy('name');

        if ($organizationId !== null) {
            $query->where('organization_id', $organizationId);
        }

        return $query->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = Team::query()->with('leader');

        if ($query->organizationId !== null) {
            $builder->where('organization_id', $query->organizationId);
        }

        $this->applyListQuery($builder, $query, ['name', 'code', 'description']);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?Team
    {
        return Team::query()->with('leader')->find($id);
    }

    public function create(array $attributes): Team
    {
        return Team::query()->create($attributes);
    }

    public function update(Team $team, array $attributes): Team
    {
        $team->fill($attributes);
        $team->save();

        return $team->refresh();
    }

    public function delete(Team $team): void
    {
        $team->delete();
    }
}
