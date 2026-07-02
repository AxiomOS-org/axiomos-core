<?php

declare(strict_types=1);

namespace Modules\Users\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Users\Application\Support\ListQuery;
use Modules\Users\Domain\Models\User;

interface UserRepositoryInterface
{
    /** @return Collection<int, User> */
    public function all(): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?User;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): User;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(User $user, array $attributes): User;

    public function delete(User $user): void;
}
