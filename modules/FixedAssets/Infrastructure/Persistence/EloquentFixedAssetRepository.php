<?php
declare(strict_types=1);
namespace Modules\FixedAssets\Infrastructure\Persistence;
use Modules\FixedAssets\Domain\Models\FixedAsset;
use Modules\FixedAssets\Domain\Repositories\Contracts\FixedAssetRepositoryInterface;
final class EloquentFixedAssetRepository implements FixedAssetRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return FixedAsset::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (FixedAsset $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?FixedAsset { return FixedAsset::query()->find($id); }
    public function create(array $attributes): FixedAsset { return FixedAsset::query()->create($attributes); }
    public function update(FixedAsset $model, array $attributes): FixedAsset { $model->fill($attributes); $model->save(); return $model; }
}