<?php

declare(strict_types=1);

namespace Modules\Identity\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\Device;
use Modules\Identity\Domain\Repositories\Contracts\DeviceRepositoryInterface;
use RuntimeException;

final class DeviceService
{
    public function __construct(
        private readonly DeviceRepositoryInterface $repository,
        private readonly IdentityPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, Device> */
    public function list(?string $identityId = null): Collection
    {
        return $this->repository->all($identityId);
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): Device
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('Device "%s" not found.', $id));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Device
    {
        $device = $this->repository->create($attributes);
        $this->platform->onCreated($device);

        return $device;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(string $id, array $attributes): Device
    {
        $device = $this->get($id);
        $before = $device->toAuditSnapshot();
        $updated = $this->repository->update($device, $attributes);
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $device = $this->get($id);
        $this->platform->onDeleted($device);
        $this->repository->delete($device);
    }
}
