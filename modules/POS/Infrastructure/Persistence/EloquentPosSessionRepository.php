<?php
declare(strict_types=1);
namespace Modules\POS\Infrastructure\Persistence;
use Modules\POS\Domain\Models\PosSession;
use Modules\POS\Domain\Repositories\Contracts\PosSessionRepositoryInterface;
final class EloquentPosSessionRepository implements PosSessionRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return PosSession::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (PosSession $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?PosSession { return PosSession::query()->find($id); }
    public function create(array $attributes): PosSession { return PosSession::query()->create($attributes); }
    public function update(PosSession $model, array $attributes): PosSession { $model->fill($attributes); $model->save(); return $model; }
}