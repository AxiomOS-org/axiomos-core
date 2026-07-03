<?php
declare(strict_types=1);
namespace Modules\CRM\Domain\Repositories\Contracts;
use Modules\CRM\Domain\Models\CrmActivity;
interface CrmActivityRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?CrmActivity;
    public function create(array $attributes): CrmActivity;
    public function update(CrmActivity $model, array $attributes): CrmActivity;
}