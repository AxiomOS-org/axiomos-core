<?php

declare(strict_types=1);

namespace Modules\Identity\Application\Services;

use App\Platform\Activity\ActivityService;
use App\Platform\Activity\ActivityType;
use App\Platform\AI\AiContextService;
use App\Platform\Audit\AuditTrailService;
use App\Platform\Notifications\NotificationService;
use App\Platform\Support\AuditableInterface;
use App\Platform\Support\PlatformEntityInterface;
use App\Platform\Support\VersionableInterface;
use App\Platform\Versioning\VersionHistoryService;

/**
 * Wires Identity entities into platform services (audit, timeline, AI, notifications).
 */
final class IdentityPlatformHooks
{
    public function __construct(
        private readonly AuditTrailService $audit,
        private readonly ActivityService $activities,
        private readonly AiContextService $aiContext,
        private readonly NotificationService $notifications,
        private readonly VersionHistoryService $versions,
    ) {
    }

    public function onCreated(PlatformEntityInterface $entity): void
    {
        $this->audit->record(
            $entity,
            'created',
            null,
            $entity instanceof AuditableInterface ? $entity->toAuditSnapshot() : null,
        );

        $this->activities->record(
            $entity,
            ActivityType::Created,
            'Record created',
            null,
            null,
            ['entity_type' => $entity->getEntityType()],
        );

        $this->aiContext->upsert(
            $entity,
            'default',
            [
                'entity_type' => $entity->getEntityType(),
                'summary' => 'New Identity record',
            ],
        );

        $this->notifications->create(
            userId: null,
            entity: $entity,
            title: 'Record created',
            body: sprintf('%s was created.', $entity->getEntityType()),
        );

        if ($entity instanceof VersionableInterface) {
            $this->versions->recordVersion($entity, $entity->toVersionSnapshot());
        }
    }

    /**
     * @param array<string, mixed> $before
     */
    public function onUpdated(PlatformEntityInterface $entity, array $before): void
    {
        $after = $entity instanceof AuditableInterface ? $entity->toAuditSnapshot() : null;

        $this->audit->record($entity, 'updated', $before, $after);

        $this->activities->record(
            $entity,
            ActivityType::Updated,
            'Record updated',
        );

        if ($entity instanceof VersionableInterface) {
            $this->versions->recordVersion($entity, $entity->toVersionSnapshot());
        }
    }

    public function onDeleted(PlatformEntityInterface $entity): void
    {
        $before = $entity instanceof AuditableInterface
            ? $entity->toAuditSnapshot()
            : null;

        $this->audit->record($entity, 'deleted', $before, null);

        $this->activities->record(
            $entity,
            ActivityType::Deleted,
            'Record deleted',
        );
    }
}
