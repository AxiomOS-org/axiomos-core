<?php
declare(strict_types=1);
namespace Modules\HR\Domain\Repositories\Contracts;
use Modules\HR\Domain\Models\PayrollRun;
interface PayrollRunRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?PayrollRun;
    public function create(array $attributes): PayrollRun;
    public function update(PayrollRun $model, array $attributes): PayrollRun;
}