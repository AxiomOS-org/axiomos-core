<?php
declare(strict_types=1);
namespace Modules\Manufacturing\Domain\Repositories\Contracts;
use Modules\Manufacturing\Domain\Models\WorkOrder;
interface WorkOrderRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?WorkOrder;
    public function create(array $attributes): WorkOrder;
    public function update(WorkOrder $model, array $attributes): WorkOrder;
}