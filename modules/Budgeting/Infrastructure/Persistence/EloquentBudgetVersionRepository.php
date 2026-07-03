<?php
declare(strict_types=1);
namespace Modules\Budgeting\Infrastructure\Persistence;
use Modules\Budgeting\Domain\Models\BudgetVersion;
use Modules\Budgeting\Domain\Repositories\Contracts\BudgetVersionRepositoryInterface;
final class EloquentBudgetVersionRepository implements BudgetVersionRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return BudgetVersion::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (BudgetVersion $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?BudgetVersion { return BudgetVersion::query()->find($id); }
    public function create(array $attributes): BudgetVersion { return BudgetVersion::query()->create($attributes); }
    public function update(BudgetVersion $model, array $attributes): BudgetVersion { $model->fill($attributes); $model->save(); return $model; }
}