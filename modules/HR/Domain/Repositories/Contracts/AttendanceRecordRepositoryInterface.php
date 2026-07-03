<?php
declare(strict_types=1);
namespace Modules\HR\Domain\Repositories\Contracts;
use Modules\HR\Domain\Models\AttendanceRecord;
interface AttendanceRecordRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?AttendanceRecord;
    public function create(array $attributes): AttendanceRecord;
    public function update(AttendanceRecord $model, array $attributes): AttendanceRecord;
}