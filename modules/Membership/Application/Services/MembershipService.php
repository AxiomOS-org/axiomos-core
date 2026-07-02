<?php

declare(strict_types=1);

namespace Modules\Membership\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Membership\Application\DTOs\CreateMembershipDTO;
use Modules\Membership\Application\DTOs\UpdateMembershipDTO;
use Modules\Membership\Application\Support\ListQuery;
use Modules\Membership\Domain\Models\Membership;
use Modules\Membership\Domain\Repositories\Contracts\MembershipRepositoryInterface;
use Modules\Organization\Domain\Models\Organization;
use Modules\Users\Domain\Models\User;
use RuntimeException;

final class MembershipService
{
    public function __construct(
        private readonly MembershipRepositoryInterface $repository,
        private readonly MembershipPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, Membership> */
    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): Membership
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('Membership "%s" not found.', $id));
    }

    public function create(CreateMembershipDTO $dto): Membership
    {
        $this->assertUserAndOrganization($dto->userId, $dto->organizationId);
        $membership = $this->repository->create($dto->toAttributes());
        $this->platform->onCreated($membership);

        return $membership;
    }

    public function update(string $id, UpdateMembershipDTO $dto): Membership
    {
        $membership = $this->get($id);
        $before = $membership->toAuditSnapshot();
        $attributes = $dto->toAttributes();

        $this->assertUserAndOrganization(
            (string) ($attributes['user_id'] ?? $membership->user_id),
            (string) ($attributes['organization_id'] ?? $membership->organization_id),
        );

        $updated = $this->repository->update($membership, $attributes);
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $membership = $this->get($id);
        $this->platform->onDeleted($membership);
        $this->repository->delete($membership);
    }

    private function assertUserAndOrganization(string $userId, string $organizationId): void
    {
        if (! User::query()->whereKey($userId)->exists()) {
            throw new RuntimeException(sprintf('User "%s" does not exist.', $userId));
        }

        if (! Organization::query()->whereKey($organizationId)->exists()) {
            throw new RuntimeException(sprintf('Organization "%s" does not exist.', $organizationId));
        }
    }
}
