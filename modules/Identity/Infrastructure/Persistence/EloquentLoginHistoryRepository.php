<?php

declare(strict_types=1);

namespace Modules\Identity\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\LoginHistory;
use Modules\Identity\Domain\Repositories\Contracts\LoginHistoryRepositoryInterface;
use Modules\Identity\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentLoginHistoryRepository implements LoginHistoryRepositoryInterface
{
    use AppliesListQuery;

    public function all(?string $identityId = null): Collection
    {
        $query = LoginHistory::query()->orderByDesc('logged_at');

        if ($identityId !== null) {
            $query->where('identity_id', $identityId);
        }

        return $query->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = LoginHistory::query();

        if ($query->identityId !== null) {
            $builder->where('identity_id', $query->identityId);
        }

        $this->applyListQuery($builder, $query, ['user_agent', 'ip_address']);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?LoginHistory
    {
        return LoginHistory::query()->find($id);
    }

    public function create(array $attributes): LoginHistory
    {
        return LoginHistory::query()->create($attributes);
    }

    public function update(LoginHistory $history, array $attributes): LoginHistory
    {
        $history->fill($attributes);
        $history->save();

        return $history->refresh();
    }

    public function delete(LoginHistory $history): void
    {
        $history->delete();
    }
}
