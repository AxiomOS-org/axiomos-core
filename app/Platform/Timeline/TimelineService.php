<?php

declare(strict_types=1);

namespace App\Platform\Timeline;

use App\Platform\Activity\ActivityService;
use App\Platform\Support\PlatformEntityInterface;

final class TimelineService
{
    public function __construct(private readonly ActivityService $activities)
    {
    }

    public function timeline(PlatformEntityInterface $entity, int $limit = 50): array
    {
        return $this->activities->timeline($entity, $limit);
    }
}

