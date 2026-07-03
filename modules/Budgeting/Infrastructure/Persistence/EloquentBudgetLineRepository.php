<?php
declare(strict_types=1);
namespace Modules\Budgeting\Infrastructure\Persistence;
use Modules\Budgeting\Domain\Models\BudgetLine;
use Modules\Budgeting\Domain\Repositories\Contracts\BudgetLineRepositoryInterface;
final class EloquentBudgetLineRepository implements BudgetLineRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return BudgetLine::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (BudgetLine $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?BudgetLine { return BudgetLine::query()->find($id); }
    public function create(array $attributes): BudgetLine { return BudgetLine::query()->create($attributes); }
    public function update(BudgetLine $model, array $attributes): BudgetLine { $model->fill($attributes); $model->save(); return $model; }
}