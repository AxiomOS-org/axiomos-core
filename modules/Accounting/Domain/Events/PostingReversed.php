<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Events;

final readonly class PostingReversed
{
    public function __construct(
        public string $originalJournalId,
        public string $reversalJournalId,
        public string $companyId,
    ) {
    }
}
