<?php
declare(strict_types=1);
namespace Modules\POS\Infrastructure\Persistence;
use Modules\POS\Domain\Models\PosOrder;
use Modules\POS\Domain\Repositories\Contracts\PosOrderRepositoryInterface;
final class EloquentPosOrderRepository implements PosOrderRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return PosOrder::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (PosOrder $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?PosOrder { return PosOrder::query()->find($id); }
    public function create(array $attributes): PosOrder { return PosOrder::query()->create($attributes); }
    public function update(PosOrder $model, array $attributes): PosOrder { $model->fill($attributes); $model->save(); return $model; }
}