<?php
declare(strict_types=1);
namespace Modules\Manufacturing\Domain\Repositories\Contracts;
use Modules\Manufacturing\Domain\Models\BillOfMaterial;
interface BillOfMaterialRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?BillOfMaterial;
    public function create(array $attributes): BillOfMaterial;
    public function update(BillOfMaterial $model, array $attributes): BillOfMaterial;
}