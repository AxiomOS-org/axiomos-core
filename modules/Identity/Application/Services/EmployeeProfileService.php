<?php

declare(strict_types=1);

namespace Modules\Identity\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\EmployeeProfile;
use Modules\Identity\Domain\Repositories\Contracts\EmployeeProfileRepositoryInterface;
use RuntimeException;

final class EmployeeProfileService
{
    public function __construct(
        private readonly EmployeeProfileRepositoryInterface $repository,
        private readonly IdentityPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, EmployeeProfile> */
    public function list(?string $identityId = null): Collection
    {
        return $this->repository->all($identityId);
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): EmployeeProfile
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('Employee profile "%s" not found.', $id));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): EmployeeProfile
    {
        $profile = $this->repository->create($attributes);
        $this->platform->onCreated($profile);

        return $profile;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(string $id, array $attributes): EmployeeProfile
    {
        $profile = $this->get($id);
        $before = $profile->toAuditSnapshot();
        $updated = $this->repository->update($profile, $attributes);
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $profile = $this->get($id);
        $this->platform->onDeleted($profile);
        $this->repository->delete($profile);
    }
}
