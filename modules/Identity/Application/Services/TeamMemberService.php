<?php

declare(strict_types=1);

namespace Modules\Identity\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\TeamMember;
use Modules\Identity\Domain\Repositories\Contracts\TeamMemberRepositoryInterface;
use RuntimeException;

final class TeamMemberService
{
    public function __construct(
        private readonly TeamMemberRepositoryInterface $repository,
        private readonly IdentityPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, TeamMember> */
    public function list(?string $teamId = null, ?string $identityId = null): Collection
    {
        return $this->repository->all($teamId, $identityId);
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): TeamMember
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('Team member "%s" not found.', $id));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): TeamMember
    {
        $member = $this->repository->create($attributes);
        $this->platform->onCreated($member);

        return $member;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(string $id, array $attributes): TeamMember
    {
        $member = $this->get($id);
        $before = $member->toAuditSnapshot();
        $updated = $this->repository->update($member, $attributes);
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $member = $this->get($id);
        $this->platform->onDeleted($member);
        $this->repository->delete($member);
    }
}
