<?php
declare(strict_types=1);
namespace Modules\Sales\Domain\Repositories\Contracts;
use Modules\Sales\Domain\Models\SalesOrder;
interface SalesOrderRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?SalesOrder;
    public function create(array $attributes): SalesOrder;
    public function update(SalesOrder $model, array $attributes): SalesOrder;
}