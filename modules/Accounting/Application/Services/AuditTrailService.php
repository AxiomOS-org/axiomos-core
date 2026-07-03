<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\Services;

use Modules\Accounting\Domain\Models\PostingLog;
use Modules\Accounting\Domain\Repositories\Contracts\PostingLogRepositoryInterface;

final class AuditTrailService
{
    public function __construct(private readonly PostingLogRepositoryInterface $postingLogs)
    {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function list(string $companyId): array
    {
        return $this->postingLogs->byCompany($companyId)
            ->map(static fn (PostingLog $log): array => $log->toArray())
            ->all();
    }
}
