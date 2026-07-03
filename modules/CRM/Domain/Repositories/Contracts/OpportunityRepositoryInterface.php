<?php
declare(strict_types=1);
namespace Modules\CRM\Domain\Repositories\Contracts;
use Modules\CRM\Domain\Models\Opportunity;
interface OpportunityRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?Opportunity;
    public function create(array $attributes): Opportunity;
    public function update(Opportunity $model, array $attributes): Opportunity;
}