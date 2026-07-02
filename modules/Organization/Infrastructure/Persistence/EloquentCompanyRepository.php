<?php

declare(strict_types=1);

namespace Modules\Organization\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Domain\Models\Company;
use Modules\Organization\Domain\Repositories\Contracts\CompanyRepositoryInterface;
use Modules\Organization\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentCompanyRepository implements CompanyRepositoryInterface
{
    use AppliesListQuery;

    public function all(?string $organizationId = null): Collection
    {
        $query = Company::query()->with('organization')->orderBy('name');

        if ($organizationId !== null) {
            $query->where('organization_id', $organizationId);
        }

        return $query->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = Company::query()->with('organization');

        if ($query->organizationId !== null) {
            $builder->where('organization_id', $query->organizationId);
        }

        $this->applyListQuery($builder, $query, ['name', 'code', 'slug', 'description']);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?Company
    {
        return Company::query()->with('organization')->find($id);
    }

    public function create(array $attributes): Company
    {
        return Company::query()->create($attributes);
    }

    public function update(Company $company, array $attributes): Company
    {
        $company->fill($attributes);
        $company->save();

        return $company->refresh();
    }

    public function delete(Company $company): void
    {
        $company->delete();
    }
}

