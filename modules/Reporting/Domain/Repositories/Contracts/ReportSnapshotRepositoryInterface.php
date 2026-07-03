<?php
declare(strict_types=1);
namespace Modules\Reporting\Domain\Repositories\Contracts;
use Modules\Reporting\Domain\Models\ReportSnapshot;
interface ReportSnapshotRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?ReportSnapshot;
    public function create(array $attributes): ReportSnapshot;
    public function update(ReportSnapshot $model, array $attributes): ReportSnapshot;
}