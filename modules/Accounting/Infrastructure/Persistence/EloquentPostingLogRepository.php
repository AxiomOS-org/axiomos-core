<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\Accounting\Domain\Models\PostingLog;
use Modules\Accounting\Domain\Repositories\Contracts\PostingLogRepositoryInterface;

final class EloquentPostingLogRepository implements PostingLogRepositoryInterface
{
    public function findByIdempotencyKey(string $key): ?PostingLog
    {
        return PostingLog::query()->where('idempotency_key', $key)->first();
    }

    public function create(array $attributes): PostingLog
    {
        return PostingLog::query()->create($attributes);
    }

    public function update(PostingLog $log, array $attributes): PostingLog
    {
        $log->fill($attributes);
        $log->save();

        return $log->refresh();
    }

    public function byCompany(string $companyId, int $limit = 200): Collection
    {
        return PostingLog::query()
            ->where('company_id', $companyId)
            ->orderByDesc('processed_at')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
