<?php

declare(strict_types=1);

namespace Modules\Identity\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\DTOs\CreateIdentityDTO;
use Modules\Identity\Application\DTOs\UpdateIdentityDTO;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\Identity;
use Modules\Identity\Domain\Repositories\Contracts\IdentityRepositoryInterface;
use RuntimeException;

final class IdentityService
{
    public function __construct(
        private readonly IdentityRepositoryInterface $repository,
        private readonly IdentityPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, Identity> */
    public function list(?string $organizationId = null): Collection
    {
        return $this->repository->all($organizationId);
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): Identity
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('Identity "%s" not found.', $id));
    }

    public function create(CreateIdentityDTO $dto): Identity
    {
        $identity = $this->repository->create($dto->toAttributes());
        $this->platform->onCreated($identity);

        return $identity;
    }

    public function update(string $id, UpdateIdentityDTO $dto): Identity
    {
        $identity = $this->get($id);
        $before = $identity->toAuditSnapshot();
        $updated = $this->repository->update($identity, $dto->toAttributes());
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $identity = $this->get($id);
        $this->platform->onDeleted($identity);
        $this->repository->delete($identity);
    }
}
