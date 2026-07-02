<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\Identity;

interface IdentityRepositoryInterface
{
    /** @return Collection<int, Identity> */
    public function all(?string $organizationId = null): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?Identity;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Identity;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Identity $identity, array $attributes): Identity;

    public function delete(Identity $identity): void;
}
