<?php
declare(strict_types=1);
namespace Modules\Inventory\Infrastructure\Persistence;
use Modules\Inventory\Domain\Models\StockMovement;
use Modules\Inventory\Domain\Repositories\Contracts\StockMovementRepositoryInterface;
final class EloquentStockMovementRepository implements StockMovementRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return StockMovement::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (StockMovement $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?StockMovement { return StockMovement::query()->find($id); }
    public function create(array $attributes): StockMovement { return StockMovement::query()->create($attributes); }
    public function update(StockMovement $model, array $attributes): StockMovement { $model->fill($attributes); $model->save(); return $model; }
}