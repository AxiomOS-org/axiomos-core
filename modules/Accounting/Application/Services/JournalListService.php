<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\Services;

use Modules\Accounting\Domain\Models\Journal;
use Modules\Accounting\Domain\Repositories\Contracts\JournalRepositoryInterface;

final class JournalListService
{
    public function __construct(private readonly JournalRepositoryInterface $journals)
    {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function list(string $companyId): array
    {
        return $this->journals->byCompany($companyId)
            ->map(static fn (Journal $journal): array => $journal->toArray())
            ->all();
    }
}
