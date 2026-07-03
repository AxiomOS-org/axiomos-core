<?php
declare(strict_types=1);
namespace Modules\Purchase\Domain\Repositories\Contracts;
use Modules\Purchase\Domain\Models\PurchaseBill;
interface PurchaseBillRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?PurchaseBill;
    public function create(array $attributes): PurchaseBill;
    public function update(PurchaseBill $model, array $attributes): PurchaseBill;
}