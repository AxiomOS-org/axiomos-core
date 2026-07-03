<?php
declare(strict_types=1);
namespace Modules\HR\Infrastructure\Persistence;
use Modules\HR\Domain\Models\PayrollRun;
use Modules\HR\Domain\Repositories\Contracts\PayrollRunRepositoryInterface;
final class EloquentPayrollRunRepository implements PayrollRunRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return PayrollRun::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (PayrollRun $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?PayrollRun { return PayrollRun::query()->find($id); }
    public function create(array $attributes): PayrollRun { return PayrollRun::query()->create($attributes); }
    public function update(PayrollRun $model, array $attributes): PayrollRun { $model->fill($attributes); $model->save(); return $model; }
}