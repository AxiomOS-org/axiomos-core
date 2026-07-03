<?php
declare(strict_types=1);
namespace Modules\Reporting\Infrastructure\Persistence;
use Modules\Reporting\Domain\Models\ReportDefinition;
use Modules\Reporting\Domain\Repositories\Contracts\ReportDefinitionRepositoryInterface;
final class EloquentReportDefinitionRepository implements ReportDefinitionRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return ReportDefinition::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (ReportDefinition $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?ReportDefinition { return ReportDefinition::query()->find($id); }
    public function create(array $attributes): ReportDefinition { return ReportDefinition::query()->create($attributes); }
    public function update(ReportDefinition $model, array $attributes): ReportDefinition { $model->fill($attributes); $model->save(); return $model; }
}