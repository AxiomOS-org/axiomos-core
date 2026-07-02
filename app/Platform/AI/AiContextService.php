<?php

declare(strict_types=1);

namespace App\Platform\AI;

use App\Platform\Support\PlatformEntityInterface;

final class AiContextService
{
    public function upsert(
        PlatformEntityInterface $entity,
        string $contextKey,
        array $context,
        ?array $metadata = null,
        ?string $updatedBy = null,
    ): AiContext {
        $ai = AiContext::query()->firstOrNew([
            'entity_type' => $entity->getEntityType(),
            'entity_id' => $entity->getEntityId(),
            'context_key' => $contextKey,
        ]);

        $ai->context = $context;
        $ai->metadata = $metadata;
        $ai->updated_by = $updatedBy;
        $ai->save();

        return $ai;
    }
}

