<?php
declare(strict_types=1);
namespace Modules\Purchase\Domain\Repositories\Contracts;
use Modules\Purchase\Domain\Models\Vendor;
interface VendorRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?Vendor;
    public function create(array $attributes): Vendor;
    public function update(Vendor $model, array $attributes): Vendor;
}