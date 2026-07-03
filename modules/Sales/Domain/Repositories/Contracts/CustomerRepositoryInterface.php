<?php
declare(strict_types=1);
namespace Modules\Sales\Domain\Repositories\Contracts;
use Modules\Sales\Domain\Models\Customer;
interface CustomerRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?Customer;
    public function create(array $attributes): Customer;
    public function update(Customer $model, array $attributes): Customer;
}