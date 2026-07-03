<?php
declare(strict_types=1);
namespace Modules\Inventory\Domain\Repositories\Contracts;
use Modules\Inventory\Domain\Models\Item;
interface ItemRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?Item;
    public function create(array $attributes): Item;
    public function update(Item $model, array $attributes): Item;
}