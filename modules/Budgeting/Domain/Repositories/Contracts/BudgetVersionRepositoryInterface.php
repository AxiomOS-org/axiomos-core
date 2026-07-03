<?php
declare(strict_types=1);
namespace Modules\Budgeting\Domain\Repositories\Contracts;
use Modules\Budgeting\Domain\Models\BudgetVersion;
interface BudgetVersionRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?BudgetVersion;
    public function create(array $attributes): BudgetVersion;
    public function update(BudgetVersion $model, array $attributes): BudgetVersion;
}