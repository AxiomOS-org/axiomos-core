<?php
declare(strict_types=1);
namespace Modules\HR\Domain\Repositories\Contracts;
use Modules\HR\Domain\Models\Employee;
interface EmployeeRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?Employee;
    public function create(array $attributes): Employee;
    public function update(Employee $model, array $attributes): Employee;
}