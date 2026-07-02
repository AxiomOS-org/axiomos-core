<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Events;

final readonly class PostingSubmitted
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public string $journalId,
        public string $companyId,
        public array $payload,
    ) {
    }
}
