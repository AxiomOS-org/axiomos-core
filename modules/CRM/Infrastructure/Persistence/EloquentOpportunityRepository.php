<?php
declare(strict_types=1);
namespace Modules\CRM\Infrastructure\Persistence;
use Modules\CRM\Domain\Models\Opportunity;
use Modules\CRM\Domain\Repositories\Contracts\OpportunityRepositoryInterface;
final class EloquentOpportunityRepository implements OpportunityRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return Opportunity::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (Opportunity $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?Opportunity { return Opportunity::query()->find($id); }
    public function create(array $attributes): Opportunity { return Opportunity::query()->create($attributes); }
    public function update(Opportunity $model, array $attributes): Opportunity { $model->fill($attributes); $model->save(); return $model; }
}