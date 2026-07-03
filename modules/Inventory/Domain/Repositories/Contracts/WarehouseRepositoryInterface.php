<?php
declare(strict_types=1);
namespace Modules\Inventory\Domain\Repositories\Contracts;
use Modules\Inventory\Domain\Models\Warehouse;
interface WarehouseRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?Warehouse;
    public function create(array $attributes): Warehouse;
    public function update(Warehouse $model, array $attributes): Warehouse;
}