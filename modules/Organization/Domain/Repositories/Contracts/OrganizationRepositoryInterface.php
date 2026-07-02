<?php

declare(strict_types=1);

namespace Modules\Organization\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Domain\Models\Organization;

interface OrganizationRepositoryInterface
{
    /** @return Collection<int, Organization> */
    public function all(): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?Organization;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Organization;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Organization $organization, array $attributes): Organization;

    public function delete(Organization $organization): void;
}
