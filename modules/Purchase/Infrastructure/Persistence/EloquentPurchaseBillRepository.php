<?php
declare(strict_types=1);
namespace Modules\Purchase\Infrastructure\Persistence;
use Modules\Purchase\Domain\Models\PurchaseBill;
use Modules\Purchase\Domain\Repositories\Contracts\PurchaseBillRepositoryInterface;
final class EloquentPurchaseBillRepository implements PurchaseBillRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return PurchaseBill::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (PurchaseBill $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?PurchaseBill { return PurchaseBill::query()->find($id); }
    public function create(array $attributes): PurchaseBill { return PurchaseBill::query()->create($attributes); }
    public function update(PurchaseBill $model, array $attributes): PurchaseBill { $model->fill($attributes); $model->save(); return $model; }
}