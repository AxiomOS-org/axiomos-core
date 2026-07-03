<?php
declare(strict_types=1);
namespace Modules\POS\Infrastructure\Persistence;
use Modules\POS\Domain\Models\PosTerminal;
use Modules\POS\Domain\Repositories\Contracts\PosTerminalRepositoryInterface;
final class EloquentPosTerminalRepository implements PosTerminalRepositoryInterface {
    public function listByCompany(string $companyId): array {
        return PosTerminal::query()->where('company_id', $companyId)->orderBy('created_at', 'desc')->get()->map(static fn (PosTerminal $m): array => $m->toArray())->all();
    }
    public function find(string $id): ?PosTerminal { return PosTerminal::query()->find($id); }
    public function create(array $attributes): PosTerminal { return PosTerminal::query()->create($attributes); }
    public function update(PosTerminal $model, array $attributes): PosTerminal { $model->fill($attributes); $model->save(); return $model; }
}