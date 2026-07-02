<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Users\Application\Support\ListQuery;
use Modules\Users\Domain\Models\User;
use Modules\Users\Domain\Repositories\Contracts\UserRepositoryInterface;
use Modules\Users\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentUserRepository implements UserRepositoryInterface
{
    use AppliesListQuery;

    public function all(): Collection
    {
        return User::query()->orderBy('display_name')->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = User::query();
        $this->applyListQuery($builder, $query, ['display_name', 'username', 'email']);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?User
    {
        return User::query()->find($id);
    }

    public function create(array $attributes): User
    {
        return User::query()->create($attributes);
    }

    public function update(User $user, array $attributes): User
    {
        $user->fill($attributes);
        $user->save();

        return $user->refresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
