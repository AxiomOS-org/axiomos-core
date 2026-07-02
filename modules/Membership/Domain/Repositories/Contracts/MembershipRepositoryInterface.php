<?php

declare(strict_types=1);

namespace Modules\Membership\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Membership\Application\Support\ListQuery;
use Modules\Membership\Domain\Models\Membership;

interface MembershipRepositoryInterface
{
    /** @return Collection<int, Membership> */
    public function all(): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?Membership;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Membership;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Membership $membership, array $attributes): Membership;

    public function delete(Membership $membership): void;
}
