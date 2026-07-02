<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\EmployeeProfile;

interface EmployeeProfileRepositoryInterface
{
    /** @return Collection<int, EmployeeProfile> */
    public function all(?string $identityId = null): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?EmployeeProfile;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): EmployeeProfile;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(EmployeeProfile $profile, array $attributes): EmployeeProfile;

    public function delete(EmployeeProfile $profile): void;
}
