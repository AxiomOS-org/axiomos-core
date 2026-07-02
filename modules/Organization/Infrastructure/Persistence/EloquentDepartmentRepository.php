<?php

declare(strict_types=1);

namespace Modules\Organization\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Domain\Models\Department;
use Modules\Organization\Domain\Repositories\Contracts\DepartmentRepositoryInterface;
use Modules\Organization\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentDepartmentRepository implements DepartmentRepositoryInterface
{
    use AppliesListQuery;

    public function all(?string $branchId = null): Collection
    {
        $query = Department::query()->with(['branch', 'parent'])->orderBy('name');

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = Department::query()->with(['branch', 'parent']);

        if ($query->branchId !== null) {
            $builder->where('branch_id', $query->branchId);
        }

        $this->applyListQuery($builder, $query, ['name', 'code', 'slug', 'description']);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?Department
    {
        return Department::query()->with(['branch', 'parent'])->find($id);
    }

    public function create(array $attributes): Department
    {
        return Department::query()->create($attributes);
    }

    public function update(Department $department, array $attributes): Department
    {
        $department->fill($attributes);
        $department->save();

        return $department->refresh();
    }

    public function delete(Department $department): void
    {
        $department->delete();
    }
}

