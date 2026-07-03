<?php
declare(strict_types=1);
namespace Modules\Inventory\Domain\Repositories\Contracts;
use Modules\Inventory\Domain\Models\StockMovement;
interface StockMovementRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?StockMovement;
    public function create(array $attributes): StockMovement;
    public function update(StockMovement $model, array $attributes): StockMovement;
}