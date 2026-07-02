<?php

declare(strict_types=1);

namespace Modules\Identity\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\EmployeeProfile;
use Modules\Identity\Domain\Repositories\Contracts\EmployeeProfileRepositoryInterface;
use Modules\Identity\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentEmployeeProfileRepository implements EmployeeProfileRepositoryInterface
{
    use AppliesListQuery;

    public function all(?string $identityId = null): Collection
    {
        $query = EmployeeProfile::query()->with('identity')->orderBy('employee_number');

        if ($identityId !== null) {
            $query->where('identity_id', $identityId);
        }

        return $query->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = EmployeeProfile::query()->with('identity');

        if ($query->identityId !== null) {
            $builder->where('identity_id', $query->identityId);
        }

        if ($query->organizationId !== null) {
            $builder->where('organization_id', $query->organizationId);
        }

        $this->applyListQuery($builder, $query, ['employee_number', 'job_title']);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?EmployeeProfile
    {
        return EmployeeProfile::query()->with('identity')->find($id);
    }

    public function create(array $attributes): EmployeeProfile
    {
        return EmployeeProfile::query()->create($attributes);
    }

    public function update(EmployeeProfile $profile, array $attributes): EmployeeProfile
    {
        $profile->fill($attributes);
        $profile->save();

        return $profile->refresh();
    }

    public function delete(EmployeeProfile $profile): void
    {
        $profile->delete();
    }
}
