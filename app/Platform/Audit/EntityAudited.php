<?php

declare(strict_types=1);

namespace App\Platform\Audit;

use App\Platform\Support\PlatformEntityInterface;

final class EntityAudited
{
    public function __construct(
        public readonly PlatformEntityInterface $entity,
        public readonly string $action,
        /** @var array<string, mixed>|null */
        public readonly ?array $oldValues = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $newValues = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $metadata = null,
        public readonly ?string $actorId = null,
    ) {
    }
}

