<?php
declare(strict_types=1);
namespace Modules\Purchase\Infrastructure\Persistence;
use Modules\Purchase\Domain\Models\Vendor;
use Modules\Purchase\Domain\Repositories\Contracts\VendorRepositoryInterface;
final class EloquentVendorRepository implements VendorRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return Vendor::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (Vendor $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?Vendor { return Vendor::query()->find($id); }
    public function create(array $attributes): Vendor { return Vendor::query()->create($attributes); }
    public function update(Vendor $model, array $attributes): Vendor { $model->fill($attributes); $model->save(); return $model; }
}