<?php

declare(strict_types=1);

namespace Modules\Authentication\Domain\Events;

final class RecordLifecycleEvent
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public readonly string $entityType,
        public readonly string $action,
        public readonly string $entityId,
        public readonly array $payload = [],
    ) {
    }
}
