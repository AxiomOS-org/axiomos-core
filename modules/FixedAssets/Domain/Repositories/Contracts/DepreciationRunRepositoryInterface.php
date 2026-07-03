<?php
declare(strict_types=1);
namespace Modules\FixedAssets\Domain\Repositories\Contracts;
use Modules\FixedAssets\Domain\Models\DepreciationRun;
interface DepreciationRunRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?DepreciationRun;
    public function create(array $attributes): DepreciationRun;
    public function update(DepreciationRun $model, array $attributes): DepreciationRun;
}