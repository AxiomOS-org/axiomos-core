<?php
declare(strict_types=1);
namespace Modules\HR\Infrastructure\Persistence;
use Modules\HR\Domain\Models\Employee;
use Modules\HR\Domain\Repositories\Contracts\EmployeeRepositoryInterface;
final class EloquentEmployeeRepository implements EmployeeRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return Employee::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (Employee $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?Employee { return Employee::query()->find($id); }
    public function create(array $attributes): Employee { return Employee::query()->create($attributes); }
    public function update(Employee $model, array $attributes): Employee { $model->fill($attributes); $model->save(); return $model; }
}