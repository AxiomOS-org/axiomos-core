<?php

declare(strict_types=1);

namespace Modules\Organization\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Domain\Models\Branch;

interface BranchRepositoryInterface
{
    /** @return Collection<int, Branch> */
    public function all(?string $companyId = null): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?Branch;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Branch;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Branch $branch, array $attributes): Branch;

    public function delete(Branch $branch): void;
}
