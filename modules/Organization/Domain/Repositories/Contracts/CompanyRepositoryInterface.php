<?php

declare(strict_types=1);

namespace Modules\Organization\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Domain\Models\Company;

interface CompanyRepositoryInterface
{
    /** @return Collection<int, Company> */
    public function all(?string $organizationId = null): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?Company;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Company;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Company $company, array $attributes): Company;

    public function delete(Company $company): void;
}
