<?php

declare(strict_types=1);

namespace Modules\Organization\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Organization\Application\DTOs\CreateEntityDTO;
use Modules\Organization\Application\DTOs\UpdateEntityDTO;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Application\Support\SlugGenerator;
use Modules\Organization\Domain\Models\Branch;
use Modules\Organization\Domain\Repositories\Contracts\BranchRepositoryInterface;
use Modules\Organization\Domain\Repositories\Contracts\CompanyRepositoryInterface;
use RuntimeException;

final class BranchService
{
    public function __construct(
        private readonly BranchRepositoryInterface $repository,
        private readonly CompanyRepositoryInterface $companies,
        private readonly SlugGenerator $slugs,
        private readonly OrganizationPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, Branch> */
    public function list(?string $companyId = null): Collection
    {
        return $this->repository->all($companyId);
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): Branch
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('Branch "%s" not found.', $id));
    }

    public function create(string $companyId, CreateEntityDTO $dto): Branch
    {
        if ($this->companies->find($companyId) === null) {
            throw new RuntimeException(sprintf('Company "%s" not found.', $companyId));
        }

        $attributes = $dto->toAttributes();
        $attributes['company_id'] = $companyId;
        $attributes['slug'] ??= $this->slugs->from($dto->name, $dto->code);

        $branch = $this->repository->create($attributes);
        $this->platform->onCreated($branch);

        return $branch;
    }

    public function update(string $id, UpdateEntityDTO $dto): Branch
    {
        $branch = $this->get($id);
        $before = $branch->toAuditSnapshot();
        $attributes = $dto->toAttributes();

        if ($dto->name !== null && $dto->slug === null) {
            $attributes['slug'] = $this->slugs->from($dto->name, $branch->code);
        }

        $updated = $this->repository->update($branch, $attributes);
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $branch = $this->get($id);
        $this->platform->onDeleted($branch);
        $this->repository->delete($branch);
    }
}
