<?php

declare(strict_types=1);

namespace App\Platform\Versioning;

use App\Platform\Support\PlatformEntityInterface;
use Illuminate\Support\Facades\DB;

final class VersionHistoryService
{
    public function recordVersion(
        PlatformEntityInterface $entity,
        array $snapshot,
        ?string $createdBy = null,
    ): EntityVersion {
        return DB::transaction(function () use ($entity, $snapshot, $createdBy): EntityVersion {
            $query = EntityVersion::query()
                ->where('entity_type', $entity->getEntityType())
                ->where('entity_id', $entity->getEntityId());

            /** @var int|null $last */
            $last = $query->max('version_number');
            $next = ($last ?? 0) + 1;

            $version = new EntityVersion();
            $version->entity_type = $entity->getEntityType();
            $version->entity_id = $entity->getEntityId();
            $version->version_number = $next;
            $version->snapshot = $snapshot;
            $version->created_by = $createdBy;
            $version->save();

            return $version;
        });
    }
}

