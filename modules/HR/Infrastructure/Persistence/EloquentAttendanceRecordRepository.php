<?php
declare(strict_types=1);
namespace Modules\HR\Infrastructure\Persistence;
use Modules\HR\Domain\Models\AttendanceRecord;
use Modules\HR\Domain\Repositories\Contracts\AttendanceRecordRepositoryInterface;
final class EloquentAttendanceRecordRepository implements AttendanceRecordRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return AttendanceRecord::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (AttendanceRecord $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?AttendanceRecord { return AttendanceRecord::query()->find($id); }
    public function create(array $attributes): AttendanceRecord { return AttendanceRecord::query()->create($attributes); }
    public function update(AttendanceRecord $model, array $attributes): AttendanceRecord { $model->fill($attributes); $model->save(); return $model; }
}