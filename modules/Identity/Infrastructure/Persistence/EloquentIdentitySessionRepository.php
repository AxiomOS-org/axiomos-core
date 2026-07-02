<?php

declare(strict_types=1);

namespace Modules\Identity\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\IdentitySession;
use Modules\Identity\Domain\Repositories\Contracts\IdentitySessionRepositoryInterface;
use Modules\Identity\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentIdentitySessionRepository implements IdentitySessionRepositoryInterface
{
    use AppliesListQuery;

    public function all(?string $identityId = null): Collection
    {
        $query = IdentitySession::query()->orderByDesc('started_at');

        if ($identityId !== null) {
            $query->where('identity_id', $identityId);
        }

        return $query->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = IdentitySession::query();

        if ($query->identityId !== null) {
            $builder->where('identity_id', $query->identityId);
        }

        $this->applyListQuery($builder, $query, ['session_token_hash', 'user_agent', 'ip_address']);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?IdentitySession
    {
        return IdentitySession::query()->find($id);
    }

    public function create(array $attributes): IdentitySession
    {
        return IdentitySession::query()->create($attributes);
    }

    public function update(IdentitySession $session, array $attributes): IdentitySession
    {
        $session->fill($attributes);
        $session->save();

        return $session->refresh();
    }

    public function delete(IdentitySession $session): void
    {
        $session->delete();
    }
}
