<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\TeamMember;

interface TeamMemberRepositoryInterface
{
    /** @return Collection<int, TeamMember> */
    public function all(?string $teamId = null, ?string $identityId = null): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?TeamMember;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): TeamMember;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(TeamMember $member, array $attributes): TeamMember;

    public function delete(TeamMember $member): void;
}