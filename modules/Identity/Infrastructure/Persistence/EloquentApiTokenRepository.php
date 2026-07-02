<?php

declare(strict_types=1);

namespace Modules\Identity\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\ApiToken;
use Modules\Identity\Domain\Repositories\Contracts\ApiTokenRepositoryInterface;
use Modules\Identity\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentApiTokenRepository implements ApiTokenRepositoryInterface
{
    use AppliesListQuery;

    public function all(?string $identityId = null): Collection
    {
        $query = ApiToken::query()->orderByDesc('created_at');

        if ($identityId !== null) {
            $query->where('identity_id', $identityId);
        }

        return $query->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = ApiToken::query();

        if ($query->identityId !== null) {
            $builder->where('identity_id', $query->identityId);
        }

        $this->applyListQuery($builder, $query, ['name', 'token_hash']);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?ApiToken
    {
        return ApiToken::query()->find($id);
    }

    public function create(array $attributes): ApiToken
    {
        return ApiToken::query()->create($attributes);
    }

    public function update(ApiToken $token, array $attributes): ApiToken
    {
        $token->fill($attributes);
        $token->save();

        return $token->refresh();
    }

    public function delete(ApiToken $token): void
    {
        $token->delete();
    }
}
