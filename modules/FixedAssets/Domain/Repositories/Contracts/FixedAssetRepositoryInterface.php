<?php
declare(strict_types=1);
namespace Modules\FixedAssets\Domain\Repositories\Contracts;
use Modules\FixedAssets\Domain\Models\FixedAsset;
interface FixedAssetRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?FixedAsset;
    public function create(array $attributes): FixedAsset;
    public function update(FixedAsset $model, array $attributes): FixedAsset;
}