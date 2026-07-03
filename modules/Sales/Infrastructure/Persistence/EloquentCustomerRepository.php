<?php
declare(strict_types=1);
namespace Modules\Sales\Infrastructure\Persistence;
use Modules\Sales\Domain\Models\Customer;
use Modules\Sales\Domain\Repositories\Contracts\CustomerRepositoryInterface;
final class EloquentCustomerRepository implements CustomerRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return Customer::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (Customer $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?Customer { return Customer::query()->find($id); }
    public function create(array $attributes): Customer { return Customer::query()->create($attributes); }
    public function update(Customer $model, array $attributes): Customer { $model->fill($attributes); $model->save(); return $model; }
}