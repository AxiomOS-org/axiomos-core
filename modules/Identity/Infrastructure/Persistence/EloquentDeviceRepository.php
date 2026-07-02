<?php

declare(strict_types=1);

namespace Modules\Identity\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\Device;
use Modules\Identity\Domain\Repositories\Contracts\DeviceRepositoryInterface;
use Modules\Identity\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentDeviceRepository implements DeviceRepositoryInterface
{
    use AppliesListQuery;

    public function all(?string $identityId = null): Collection
    {
        $query = Device::query()->orderByDesc('last_seen_at');

        if ($identityId !== null) {
            $query->where('identity_id', $identityId);
        }

        return $query->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = Device::query();

        if ($query->identityId !== null) {
            $builder->where('identity_id', $query->identityId);
        }

        $this->applyListQuery($builder, $query, ['device_type', 'fingerprint', 'user_agent']);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?Device
    {
        return Device::query()->find($id);
    }

    public function create(array $attributes): Device
    {
        return Device::query()->create($attributes);
    }

    public function update(Device $device, array $attributes): Device
    {
        $device->fill($attributes);
        $device->save();

        return $device->refresh();
    }

    public function delete(Device $device): void
    {
        $device->delete();
    }
}
