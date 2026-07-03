<?php
declare(strict_types=1);
namespace Modules\Projects\Infrastructure\Persistence;
use Modules\Projects\Domain\Models\Timesheet;
use Modules\Projects\Domain\Repositories\Contracts\TimesheetRepositoryInterface;
final class EloquentTimesheetRepository implements TimesheetRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return Timesheet::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (Timesheet $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?Timesheet { return Timesheet::query()->find($id); }
    public function create(array $attributes): Timesheet { return Timesheet::query()->create($attributes); }
    public function update(Timesheet $model, array $attributes): Timesheet { $model->fill($attributes); $model->save(); return $model; }
}