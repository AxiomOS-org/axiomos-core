<?php

declare(strict_types=1);

namespace App\Platform\Activity;

use App\Platform\Support\PlatformEntityInterface;

final class ActivityRecorded
{
    public function __construct(
        public readonly PlatformEntityInterface $entity,
        public readonly string $type,
        public readonly string $title,
        public readonly ?string $description = null,
        /** @var array<string, mixed> */
        public readonly array $metadata = [],
        public readonly ?string $actorId = null,
    ) {
    }
}

