<?php

declare(strict_types=1);

namespace Modules\Organization\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Organization\Application\DTOs\CreateEntityDTO;
use Modules\Organization\Application\DTOs\UpdateEntityDTO;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Application\Support\SlugGenerator;
use Modules\Organization\Domain\Models\Department;
use Modules\Organization\Domain\Repositories\Contracts\BranchRepositoryInterface;
use Modules\Organization\Domain\Repositories\Contracts\DepartmentRepositoryInterface;
use RuntimeException;

final class DepartmentService
{
    public function __construct(
        private readonly DepartmentRepositoryInterface $repository,
        private readonly BranchRepositoryInterface $branches,
        private readonly SlugGenerator $slugs,
        private readonly OrganizationPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, Department> */
    public function list(?string $branchId = null): Collection
    {
        return $this->repository->all($branchId);
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): Department
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('Department "%s" not found.', $id));
    }

    public function create(string $branchId, CreateEntityDTO $dto, ?string $parentId = null): Department
    {
        if ($this->branches->find($branchId) === null) {
            throw new RuntimeException(sprintf('Branch "%s" not found.', $branchId));
        }

        $attributes = $dto->toAttributes();
        $attributes['branch_id'] = $branchId;
        $attributes['parent_id'] = $parentId;
        $attributes['slug'] ??= $this->slugs->from($dto->name, $dto->code);

        $department = $this->repository->create($attributes);
        $this->platform->onCreated($department);

        return $department;
    }

    public function update(string $id, UpdateEntityDTO $dto, ?string $parentId = null): Department
    {
        $department = $this->get($id);
        $before = $department->toAuditSnapshot();
        $attributes = $dto->toAttributes();

        if ($parentId !== null) {
            $attributes['parent_id'] = $parentId;
        }

        if ($dto->name !== null && $dto->slug === null) {
            $attributes['slug'] = $this->slugs->from($dto->name, $department->code);
        }

        $updated = $this->repository->update($department, $attributes);
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $department = $this->get($id);
        $this->platform->onDeleted($department);
        $this->repository->delete($department);
    }
}
