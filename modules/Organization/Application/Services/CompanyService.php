<?php

declare(strict_types=1);

namespace Modules\Organization\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Organization\Application\DTOs\CreateEntityDTO;
use Modules\Organization\Application\DTOs\UpdateEntityDTO;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Application\Support\SlugGenerator;
use Modules\Organization\Domain\Models\Company;
use Modules\Organization\Domain\Repositories\Contracts\CompanyRepositoryInterface;
use Modules\Organization\Domain\Repositories\Contracts\OrganizationRepositoryInterface;
use RuntimeException;

final class CompanyService
{
    public function __construct(
        private readonly CompanyRepositoryInterface $repository,
        private readonly OrganizationRepositoryInterface $organizations,
        private readonly SlugGenerator $slugs,
        private readonly OrganizationPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, Company> */
    public function list(?string $organizationId = null): Collection
    {
        return $this->repository->all($organizationId);
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): Company
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('Company "%s" not found.', $id));
    }

    public function create(string $organizationId, CreateEntityDTO $dto): Company
    {
        if ($this->organizations->find($organizationId) === null) {
            throw new RuntimeException(sprintf('Organization "%s" not found.', $organizationId));
        }

        $attributes = $dto->toAttributes();
        $attributes['organization_id'] = $organizationId;
        $attributes['slug'] ??= $this->slugs->from($dto->name, $dto->code);

        $company = $this->repository->create($attributes);
        $this->platform->onCreated($company);

        return $company;
    }

    public function update(string $id, UpdateEntityDTO $dto): Company
    {
        $company = $this->get($id);
        $before = $company->toAuditSnapshot();
        $attributes = $dto->toAttributes();

        if ($dto->name !== null && $dto->slug === null) {
            $attributes['slug'] = $this->slugs->from($dto->name, $company->code);
        }

        $updated = $this->repository->update($company, $attributes);
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $company = $this->get($id);
        $this->platform->onDeleted($company);
        $this->repository->delete($company);
    }
}
