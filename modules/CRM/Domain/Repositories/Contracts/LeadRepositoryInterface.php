<?php
declare(strict_types=1);
namespace Modules\CRM\Domain\Repositories\Contracts;
use Modules\CRM\Domain\Models\Lead;
interface LeadRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?Lead;
    public function create(array $attributes): Lead;
    public function update(Lead $model, array $attributes): Lead;
}