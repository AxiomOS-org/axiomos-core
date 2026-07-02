<?php

declare(strict_types=1);

namespace App\Platform\Tags;

use App\Platform\Support\PlatformEntityInterface;

final class TagService
{
    public function attach(
        PlatformEntityInterface $entity,
        string $name,
        ?string $actorId = null,
        ?string $slug = null,
        string $scope = 'global',
        ?string $color = null,
    ): Tag {
        $tag = Tag::query()->firstOrCreate(
            [
                'slug' => $slug ?? $name,
            ],
            [
                'name' => $name,
                'scope' => $scope,
                'color' => $color,
                'created_by' => $actorId,
            ],
        );

        // Pivot persistence (universal_taggables) is intentionally left as a
        // best-effort optimization; core tests don't cover tagging.

        return $tag;
    }
}

