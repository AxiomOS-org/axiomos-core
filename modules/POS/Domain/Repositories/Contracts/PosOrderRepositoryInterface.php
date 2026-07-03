<?php
declare(strict_types=1);
namespace Modules\POS\Domain\Repositories\Contracts;
use Modules\POS\Domain\Models\PosOrder;
interface PosOrderRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?PosOrder;
    public function create(array $attributes): PosOrder;
    public function update(PosOrder $model, array $attributes): PosOrder;
}