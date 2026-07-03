<?php
declare(strict_types=1);
namespace Modules\Reporting\Infrastructure\Persistence;
use Modules\Reporting\Domain\Models\ReportSnapshot;
use Modules\Reporting\Domain\Repositories\Contracts\ReportSnapshotRepositoryInterface;
final class EloquentReportSnapshotRepository implements ReportSnapshotRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return ReportSnapshot::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (ReportSnapshot $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?ReportSnapshot { return ReportSnapshot::query()->find($id); }
    public function create(array $attributes): ReportSnapshot { return ReportSnapshot::query()->create($attributes); }
    public function update(ReportSnapshot $model, array $attributes): ReportSnapshot { $model->fill($attributes); $model->save(); return $model; }
}