<?php

declare(strict_types=1);

namespace Modules\Organization\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Domain\Models\Branch;
use Modules\Organization\Domain\Repositories\Contracts\BranchRepositoryInterface;
use Modules\Organization\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentBranchRepository implements BranchRepositoryInterface
{
    use AppliesListQuery;

    public function all(?string $companyId = null): Collection
    {
        $query = Branch::query()->with('company')->orderBy('name');

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = Branch::query()->with('company');

        if ($query->companyId !== null) {
            $builder->where('company_id', $query->companyId);
        }

        $this->applyListQuery($builder, $query, ['name', 'code', 'slug', 'description']);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?Branch
    {
        return Branch::query()->with('company')->find($id);
    }

    public function create(array $attributes): Branch
    {
        return Branch::query()->create($attributes);
    }

    public function update(Branch $branch, array $attributes): Branch
    {
        $branch->fill($attributes);
        $branch->save();

        return $branch->refresh();
    }

    public function delete(Branch $branch): void
    {
        $branch->delete();
    }
}

