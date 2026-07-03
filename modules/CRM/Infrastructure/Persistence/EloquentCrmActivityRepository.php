<?php
declare(strict_types=1);
namespace Modules\CRM\Infrastructure\Persistence;
use Modules\CRM\Domain\Models\CrmActivity;
use Modules\CRM\Domain\Repositories\Contracts\CrmActivityRepositoryInterface;
final class EloquentCrmActivityRepository implements CrmActivityRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return CrmActivity::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (CrmActivity $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?CrmActivity { return CrmActivity::query()->find($id); }
    public function create(array $attributes): CrmActivity { return CrmActivity::query()->create($attributes); }
    public function update(CrmActivity $model, array $attributes): CrmActivity { $model->fill($attributes); $model->save(); return $model; }
}