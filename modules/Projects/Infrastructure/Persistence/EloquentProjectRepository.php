<?php
declare(strict_types=1);
namespace Modules\Projects\Infrastructure\Persistence;
use Modules\Projects\Domain\Models\Project;
use Modules\Projects\Domain\Repositories\Contracts\ProjectRepositoryInterface;
final class EloquentProjectRepository implements ProjectRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return Project::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (Project $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?Project { return Project::query()->find($id); }
    public function create(array $attributes): Project { return Project::query()->create($attributes); }
    public function update(Project $model, array $attributes): Project { $model->fill($attributes); $model->save(); return $model; }
}