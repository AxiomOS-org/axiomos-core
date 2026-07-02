<?php

declare(strict_types=1);

namespace App\Platform\Comments;

use App\Platform\Support\PlatformEntityInterface;

final class CommentAdded
{
    public function __construct(
        public readonly PlatformEntityInterface $entity,
        public readonly string $body,
        public readonly ?string $actorId = null,
        public readonly ?string $parentId = null,
    ) {
    }
}

