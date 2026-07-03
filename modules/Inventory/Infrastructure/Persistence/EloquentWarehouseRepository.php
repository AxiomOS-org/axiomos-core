<?php
declare(strict_types=1);
namespace Modules\Inventory\Infrastructure\Persistence;
use Modules\Inventory\Domain\Models\Warehouse;
use Modules\Inventory\Domain\Repositories\Contracts\WarehouseRepositoryInterface;
final class EloquentWarehouseRepository implements WarehouseRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return Warehouse::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (Warehouse $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?Warehouse { return Warehouse::query()->find($id); }
    public function create(array $attributes): Warehouse { return Warehouse::query()->create($attributes); }
    public function update(Warehouse $model, array $attributes): Warehouse { $model->fill($attributes); $model->save(); return $model; }
}