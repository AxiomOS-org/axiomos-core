<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\Device;

interface DeviceRepositoryInterface
{
    /** @return Collection<int, Device> */
    public function all(?string $identityId = null): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?Device;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Device;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Device $device, array $attributes): Device;

    public function delete(Device $device): void;
}
