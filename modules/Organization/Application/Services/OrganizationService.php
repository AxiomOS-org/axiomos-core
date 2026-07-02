<?php

declare(strict_types=1);

namespace Modules\Organization\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Organization\Application\DTOs\CreateEntityDTO;
use Modules\Organization\Application\DTOs\UpdateEntityDTO;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Application\Support\SlugGenerator;
use Modules\Organization\Domain\Models\Organization;
use Modules\Organization\Domain\Repositories\Contracts\OrganizationRepositoryInterface;
use RuntimeException;

final class OrganizationService
{
    public function __construct(
        private readonly OrganizationRepositoryInterface $repository,
        private readonly SlugGenerator $slugs,
        private readonly OrganizationPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, Organization> */
    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): Organization
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('Organization "%s" not found.', $id));
    }

    public function create(CreateEntityDTO $dto): Organization
    {
        $attributes = $dto->toAttributes();
        $attributes['slug'] ??= $this->slugs->from($dto->name, $dto->code);

        $organization = $this->repository->create($attributes);
        $this->platform->onCreated($organization);

        return $organization;
    }

    public function update(string $id, UpdateEntityDTO $dto): Organization
    {
        $organization = $this->get($id);
        $before = $organization->toAuditSnapshot();
        $attributes = $dto->toAttributes();

        if ($dto->name !== null && $dto->slug === null && ! isset($attributes['slug'])) {
            $attributes['slug'] = $this->slugs->from($dto->name, $organization->code);
        }

        $updated = $this->repository->update($organization, $attributes);
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $organization = $this->get($id);
        $this->platform->onDeleted($organization);
        $this->repository->delete($organization);
    }
}
