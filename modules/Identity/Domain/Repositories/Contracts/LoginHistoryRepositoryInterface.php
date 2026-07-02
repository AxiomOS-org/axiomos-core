<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\LoginHistory;

interface LoginHistoryRepositoryInterface
{
    /** @return Collection<int, LoginHistory> */
    public function all(?string $identityId = null): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?LoginHistory;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): LoginHistory;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(LoginHistory $history, array $attributes): LoginHistory;

    public function delete(LoginHistory $history): void;
}
