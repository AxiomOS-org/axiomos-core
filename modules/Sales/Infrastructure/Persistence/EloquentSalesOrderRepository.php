<?php
declare(strict_types=1);
namespace Modules\Sales\Infrastructure\Persistence;
use Modules\Sales\Domain\Models\SalesOrder;
use Modules\Sales\Domain\Repositories\Contracts\SalesOrderRepositoryInterface;
final class EloquentSalesOrderRepository implements SalesOrderRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return SalesOrder::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (SalesOrder $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?SalesOrder { return SalesOrder::query()->find($id); }
    public function create(array $attributes): SalesOrder { return SalesOrder::query()->create($attributes); }
    public function update(SalesOrder $model, array $attributes): SalesOrder { $model->fill($attributes); $model->save(); return $model; }
}