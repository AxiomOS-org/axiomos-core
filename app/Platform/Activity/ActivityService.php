<?php

declare(strict_types=1);

namespace App\Platform\Activity;

use App\Platform\Support\PlatformEntityInterface;

final class ActivityService
{
    public function timeline(PlatformEntityInterface $entity, int $limit = 50): array
    {
        return Activity::query()
            ->where('entity_type', $entity->getEntityType())
            ->where('entity_id', $entity->getEntityId())
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function record(
        PlatformEntityInterface $entity,
        ActivityType|string $type,
        string $title,
        ?string $description = null,
        ?string $actorId = null,
        array $metadata = [],
    ): Activity {
        $activity = new Activity();
        $activity->entity_type = $entity->getEntityType();
        $activity->entity_id = $entity->getEntityId();
        $activity->type = $type instanceof ActivityType ? $type->value : $type;
        $activity->title = $title;
        $activity->description = $description;
        $activity->metadata = $metadata;
        $activity->actor_id = $actorId;
        $activity->save();

        return $activity;
    }
}

