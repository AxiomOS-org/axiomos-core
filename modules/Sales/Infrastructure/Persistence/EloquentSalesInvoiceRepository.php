<?php
declare(strict_types=1);
namespace Modules\Sales\Infrastructure\Persistence;
use Modules\Sales\Domain\Models\SalesInvoice;
use Modules\Sales\Domain\Repositories\Contracts\SalesInvoiceRepositoryInterface;
final class EloquentSalesInvoiceRepository implements SalesInvoiceRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return SalesInvoice::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (SalesInvoice $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?SalesInvoice { return SalesInvoice::query()->find($id); }
    public function create(array $attributes): SalesInvoice { return SalesInvoice::query()->create($attributes); }
    public function update(SalesInvoice $model, array $attributes): SalesInvoice { $model->fill($attributes); $model->save(); return $model; }
}