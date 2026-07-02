<?php

declare(strict_types=1);

namespace App\Platform\Support;

/**
 * Stable polymorphic reference to an entity.
 */
final class EntityReference
{
    public function __construct(
        public readonly string $entityType,
        public readonly string $entityId,
    ) {
    }

    public static function fromEntity(PlatformEntityInterface $entity): self
    {
        return new self(
            entityType: $entity->getEntityType(),
            entityId: $entity->getEntityId(),
        );
    }
}

