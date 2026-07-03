<?php
declare(strict_types=1);
namespace Modules\Projects\Domain\Repositories\Contracts;
use Modules\Projects\Domain\Models\Timesheet;
interface TimesheetRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?Timesheet;
    public function create(array $attributes): Timesheet;
    public function update(Timesheet $model, array $attributes): Timesheet;
}