<?php
declare(strict_types=1);
namespace Modules\Purchase\Infrastructure\Persistence;
use Modules\Purchase\Domain\Models\PurchaseOrder;
use Modules\Purchase\Domain\Repositories\Contracts\PurchaseOrderRepositoryInterface;
final class EloquentPurchaseOrderRepository implements PurchaseOrderRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return PurchaseOrder::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (PurchaseOrder $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?PurchaseOrder { return PurchaseOrder::query()->find($id); }
    public function create(array $attributes): PurchaseOrder { return PurchaseOrder::query()->create($attributes); }
    public function update(PurchaseOrder $model, array $attributes): PurchaseOrder { $model->fill($attributes); $model->save(); return $model; }
}