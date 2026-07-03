<?php
declare(strict_types=1);
namespace Modules\Reporting\Domain\Repositories\Contracts;
use Modules\Reporting\Domain\Models\ReportDefinition;
interface ReportDefinitionRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?ReportDefinition;
    public function create(array $attributes): ReportDefinition;
    public function update(ReportDefinition $model, array $attributes): ReportDefinition;
}