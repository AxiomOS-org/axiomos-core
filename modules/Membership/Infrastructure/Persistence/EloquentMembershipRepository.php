<?php

declare(strict_types=1);

namespace Modules\Membership\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Membership\Application\Support\ListQuery;
use Modules\Membership\Domain\Models\Membership;
use Modules\Membership\Domain\Repositories\Contracts\MembershipRepositoryInterface;
use Modules\Membership\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentMembershipRepository implements MembershipRepositoryInterface
{
    use AppliesListQuery;

    public function all(): Collection
    {
        return Membership::query()->orderByDesc('created_at')->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = Membership::query();
        $this->applyListQuery($builder, $query);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?Membership
    {
        return Membership::query()->find($id);
    }

    public function create(array $attributes): Membership
    {
        return Membership::query()->create($attributes);
    }

    public function update(Membership $membership, array $attributes): Membership
    {
        $membership->fill($attributes);
        $membership->save();

        return $membership->refresh();
    }

    public function delete(Membership $membership): void
    {
        $membership->delete();
    }
}
