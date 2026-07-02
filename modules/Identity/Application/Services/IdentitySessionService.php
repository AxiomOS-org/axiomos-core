<?php

declare(strict_types=1);

namespace Modules\Identity\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\IdentitySession;
use Modules\Identity\Domain\Repositories\Contracts\IdentitySessionRepositoryInterface;
use RuntimeException;

final class IdentitySessionService
{
    public function __construct(
        private readonly IdentitySessionRepositoryInterface $repository,
        private readonly IdentityPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, IdentitySession> */
    public function list(?string $identityId = null): Collection
    {
        return $this->repository->all($identityId);
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): IdentitySession
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('Identity session "%s" not found.', $id));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): IdentitySession
    {
        $session = $this->repository->create($attributes);
        $this->platform->onCreated($session);

        return $session;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(string $id, array $attributes): IdentitySession
    {
        $session = $this->get($id);
        $before = $session->toAuditSnapshot();
        $updated = $this->repository->update($session, $attributes);
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $session = $this->get($id);
        $this->platform->onDeleted($session);
        $this->repository->delete($session);
    }
}
