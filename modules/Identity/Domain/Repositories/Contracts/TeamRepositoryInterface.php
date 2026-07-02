<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\Team;

interface TeamRepositoryInterface
{
    /** @return Collection<int, Team> */
    public function all(?string $organizationId = null): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?Team;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Team;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Team $team, array $attributes): Team;

    public function delete(Team $team): void;
}
