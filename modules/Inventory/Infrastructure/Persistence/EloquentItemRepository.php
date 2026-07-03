<?php
declare(strict_types=1);
namespace Modules\Inventory\Infrastructure\Persistence;
use Modules\Inventory\Domain\Models\Item;
use Modules\Inventory\Domain\Repositories\Contracts\ItemRepositoryInterface;
final class EloquentItemRepository implements ItemRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return Item::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (Item $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?Item { return Item::query()->find($id); }
    public function create(array $attributes): Item { return Item::query()->create($attributes); }
    public function update(Item $model, array $attributes): Item { $model->fill($attributes); $model->save(); return $model; }
}