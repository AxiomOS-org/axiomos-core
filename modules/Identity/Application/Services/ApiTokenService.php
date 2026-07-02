<?php

declare(strict_types=1);

namespace Modules\Identity\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\ApiToken;
use Modules\Identity\Domain\Repositories\Contracts\ApiTokenRepositoryInterface;
use RuntimeException;

final class ApiTokenService
{
    public function __construct(
        private readonly ApiTokenRepositoryInterface $repository,
        private readonly IdentityPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, ApiToken> */
    public function list(?string $identityId = null): Collection
    {
        return $this->repository->all($identityId);
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): ApiToken
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('API token "%s" not found.', $id));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): ApiToken
    {
        $token = $this->repository->create($attributes);
        $this->platform->onCreated($token);

        return $token;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(string $id, array $attributes): ApiToken
    {
        $token = $this->get($id);
        $before = $token->toAuditSnapshot();
        $updated = $this->repository->update($token, $attributes);
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $token = $this->get($id);
        $this->platform->onDeleted($token);
        $this->repository->delete($token);
    }
}
