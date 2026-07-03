<?php
declare(strict_types=1);
namespace Modules\Manufacturing\Infrastructure\Persistence;
use Modules\Manufacturing\Domain\Models\WorkOrder;
use Modules\Manufacturing\Domain\Repositories\Contracts\WorkOrderRepositoryInterface;
final class EloquentWorkOrderRepository implements WorkOrderRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return WorkOrder::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (WorkOrder $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?WorkOrder { return WorkOrder::query()->find($id); }
    public function create(array $attributes): WorkOrder { return WorkOrder::query()->create($attributes); }
    public function update(WorkOrder $model, array $attributes): WorkOrder { $model->fill($attributes); $model->save(); return $model; }
}