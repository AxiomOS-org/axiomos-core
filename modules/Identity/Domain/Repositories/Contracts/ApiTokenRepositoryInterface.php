<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\ApiToken;

interface ApiTokenRepositoryInterface
{
    /** @return Collection<int, ApiToken> */
    public function all(?string $identityId = null): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?ApiToken;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): ApiToken;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(ApiToken $token, array $attributes): ApiToken;

    public function delete(ApiToken $token): void;
}
