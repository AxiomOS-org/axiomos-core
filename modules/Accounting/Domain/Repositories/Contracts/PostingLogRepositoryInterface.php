<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Repositories\Contracts;

use Illuminate\Support\Collection;
use Modules\Accounting\Domain\Models\PostingLog;

interface PostingLogRepositoryInterface
{
    public function findByIdempotencyKey(string $key): ?PostingLog;

    public function create(array $attributes): PostingLog;

    public function update(PostingLog $log, array $attributes): PostingLog;

    /** @return Collection<int, PostingLog> */
    public function byCompany(string $companyId, int $limit = 200): Collection;
}
