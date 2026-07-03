<?php
declare(strict_types=1);
namespace Modules\Manufacturing\Domain\Repositories\Contracts;
use Modules\Manufacturing\Domain\Models\ProductionRun;
interface ProductionRunRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?ProductionRun;
    public function create(array $attributes): ProductionRun;
    public function update(ProductionRun $model, array $attributes): ProductionRun;
}