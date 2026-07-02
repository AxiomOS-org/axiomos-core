<?php

declare(strict_types=1);

namespace Modules\Organization\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Domain\Models\Department;

interface DepartmentRepositoryInterface
{
    /** @return Collection<int, Department> */
    public function all(?string $branchId = null): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?Department;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Department;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Department $department, array $attributes): Department;

    public function delete(Department $department): void;
}
