<?php
declare(strict_types=1);
namespace Modules\Budgeting\Domain\Repositories\Contracts;
use Modules\Budgeting\Domain\Models\BudgetLine;
interface BudgetLineRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?BudgetLine;
    public function create(array $attributes): BudgetLine;
    public function update(BudgetLine $model, array $attributes): BudgetLine;
}