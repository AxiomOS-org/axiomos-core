<?php
declare(strict_types=1);
namespace Modules\Manufacturing\Infrastructure\Persistence;
use Modules\Manufacturing\Domain\Models\ProductionRun;
use Modules\Manufacturing\Domain\Repositories\Contracts\ProductionRunRepositoryInterface;
final class EloquentProductionRunRepository implements ProductionRunRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return ProductionRun::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (ProductionRun $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?ProductionRun { return ProductionRun::query()->find($id); }
    public function create(array $attributes): ProductionRun { return ProductionRun::query()->create($attributes); }
    public function update(ProductionRun $model, array $attributes): ProductionRun { $model->fill($attributes); $model->save(); return $model; }
}