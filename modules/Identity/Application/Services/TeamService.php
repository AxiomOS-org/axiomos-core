<?php

declare(strict_types=1);

namespace Modules\Identity\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\Team;
use Modules\Identity\Domain\Repositories\Contracts\TeamRepositoryInterface;
use RuntimeException;

final class TeamService
{
    public function __construct(
        private readonly TeamRepositoryInterface $repository,
        private readonly IdentityPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, Team> */
    public function list(?string $organizationId = null): Collection
    {
        return $this->repository->all($organizationId);
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): Team
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('Team "%s" not found.', $id));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Team
    {
        $team = $this->repository->create($attributes);
        $this->platform->onCreated($team);

        return $team;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(string $id, array $attributes): Team
    {
        $team = $this->get($id);
        $before = $team->toAuditSnapshot();
        $updated = $this->repository->update($team, $attributes);
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $team = $this->get($id);
        $this->platform->onDeleted($team);
        $this->repository->delete($team);
    }
}
