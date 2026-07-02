<?php

declare(strict_types=1);

namespace Modules\Identity\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\LoginHistory;
use Modules\Identity\Domain\Repositories\Contracts\LoginHistoryRepositoryInterface;
use RuntimeException;

final class LoginHistoryService
{
    public function __construct(
        private readonly LoginHistoryRepositoryInterface $repository,
        private readonly IdentityPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, LoginHistory> */
    public function list(?string $identityId = null): Collection
    {
        return $this->repository->all($identityId);
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): LoginHistory
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('Login history "%s" not found.', $id));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): LoginHistory
    {
        $history = $this->repository->create($attributes);
        $this->platform->onCreated($history);

        return $history;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(string $id, array $attributes): LoginHistory
    {
        $history = $this->get($id);
        $before = $history->toAuditSnapshot();
        $updated = $this->repository->update($history, $attributes);
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $history = $this->get($id);
        $this->platform->onDeleted($history);
        $this->repository->delete($history);
    }
}
