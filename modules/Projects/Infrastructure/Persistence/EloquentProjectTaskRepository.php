<?php
declare(strict_types=1);
namespace Modules\Projects\Infrastructure\Persistence;
use Modules\Projects\Domain\Models\ProjectTask;
use Modules\Projects\Domain\Repositories\Contracts\ProjectTaskRepositoryInterface;
final class EloquentProjectTaskRepository implements ProjectTaskRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return ProjectTask::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (ProjectTask $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?ProjectTask { return ProjectTask::query()->find($id); }
    public function create(array $attributes): ProjectTask { return ProjectTask::query()->create($attributes); }
    public function update(ProjectTask $model, array $attributes): ProjectTask { $model->fill($attributes); $model->save(); return $model; }
}