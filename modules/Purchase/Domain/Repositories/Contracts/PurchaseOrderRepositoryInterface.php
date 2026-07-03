<?php
declare(strict_types=1);
namespace Modules\Purchase\Domain\Repositories\Contracts;
use Modules\Purchase\Domain\Models\PurchaseOrder;
interface PurchaseOrderRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?PurchaseOrder;
    public function create(array $attributes): PurchaseOrder;
    public function update(PurchaseOrder $model, array $attributes): PurchaseOrder;
}