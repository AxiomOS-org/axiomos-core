<?php
declare(strict_types=1);
namespace Modules\CRM\Infrastructure\Persistence;
use Modules\CRM\Domain\Models\Lead;
use Modules\CRM\Domain\Repositories\Contracts\LeadRepositoryInterface;
final class EloquentLeadRepository implements LeadRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return Lead::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (Lead $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?Lead { return Lead::query()->find($id); }
    public function create(array $attributes): Lead { return Lead::query()->create($attributes); }
    public function update(Lead $model, array $attributes): Lead { $model->fill($attributes); $model->save(); return $model; }
}