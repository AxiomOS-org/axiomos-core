<?php
declare(strict_types=1);
namespace Modules\Manufacturing\Infrastructure\Persistence;
use Modules\Manufacturing\Domain\Models\BillOfMaterial;
use Modules\Manufacturing\Domain\Repositories\Contracts\BillOfMaterialRepositoryInterface;
final class EloquentBillOfMaterialRepository implements BillOfMaterialRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return BillOfMaterial::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (BillOfMaterial $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?BillOfMaterial { return BillOfMaterial::query()->find($id); }
    public function create(array $attributes): BillOfMaterial { return BillOfMaterial::query()->create($attributes); }
    public function update(BillOfMaterial $model, array $attributes): BillOfMaterial { $model->fill($attributes); $model->save(); return $model; }
}