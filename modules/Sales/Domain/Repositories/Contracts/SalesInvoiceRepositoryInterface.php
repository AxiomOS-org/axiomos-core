<?php
declare(strict_types=1);
namespace Modules\Sales\Domain\Repositories\Contracts;
use Modules\Sales\Domain\Models\SalesInvoice;
interface SalesInvoiceRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?SalesInvoice;
    public function create(array $attributes): SalesInvoice;
    public function update(SalesInvoice $model, array $attributes): SalesInvoice;
}