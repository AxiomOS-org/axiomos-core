<?php
declare(strict_types=1);
namespace Modules\POS\Domain\Repositories\Contracts;
use Modules\POS\Domain\Models\PosSession;
interface PosSessionRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?PosSession;
    public function create(array $attributes): PosSession;
    public function update(PosSession $model, array $attributes): PosSession;
}