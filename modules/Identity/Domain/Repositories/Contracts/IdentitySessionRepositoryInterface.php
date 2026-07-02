<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\IdentitySession;

interface IdentitySessionRepositoryInterface
{
    /** @return Collection<int, IdentitySession> */
    public function all(?string $identityId = null): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?IdentitySession;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): IdentitySession;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(IdentitySession $session, array $attributes): IdentitySession;

    public function delete(IdentitySession $session): void;
}
