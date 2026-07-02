<?php

declare(strict_types=1);

namespace Modules\Authorization\Domain\Events;

final readonly class RecordLifecycleEvent
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public string $entityType,
        public string $action,
        public string $entityId,
        public array $payload = [],
    ) {
    }
}
