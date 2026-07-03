<?php
declare(strict_types=1);
namespace Modules\FixedAssets\Infrastructure\Persistence;
use Modules\FixedAssets\Domain\Models\DepreciationRun;
use Modules\FixedAssets\Domain\Repositories\Contracts\DepreciationRunRepositoryInterface;
final class EloquentDepreciationRunRepository implements DepreciationRunRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return DepreciationRun::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (DepreciationRun $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?DepreciationRun { return DepreciationRun::query()->find($id); }
    public function create(array $attributes): DepreciationRun { return DepreciationRun::query()->create($attributes); }
    public function update(DepreciationRun $model, array $attributes): DepreciationRun { $model->fill($attributes); $model->save(); return $model; }
}