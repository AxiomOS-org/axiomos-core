<?php

declare(strict_types=1);

namespace Modules\Authorization\Application\Services;

use App\Platform\Activity\ActivityService;
use App\Platform\Activity\ActivityType;
use App\Platform\Audit\AuditTrailService;
use App\Platform\Support\AuditableInterface;
use App\Platform\Support\PlatformEntityInterface;
use Modules\Authorization\Application\Events\DomainEventRecorder;
use Modules\Authorization\Domain\Events\RecordLifecycleEvent;

final class AuthorizationPlatformHooks
{
    public function __construct(
        private readonly AuditTrailService $audit,
        private readonly ActivityService $activities,
    ) {
    }

    public function onCreated(PlatformEntityInterface $entity): void
    {
        $snapshot = $entity instanceof AuditableInterface ? $entity->toAuditSnapshot() : null;
        $this->audit->record($entity, 'created', null, $snapshot);
        $this->activities->record($entity, ActivityType::Created, 'Authorization record created');
        DomainEventRecorder::record(new RecordLifecycleEvent($entity->getEntityType(), 'created', $entity->getEntityId(), $snapshot ?? []));
    }

    /**
     * @param array<string, mixed> $before
     */
    public function onUpdated(PlatformEntityInterface $entity, array $before): void
    {
        $after = $entity instanceof AuditableInterface ? $entity->toAuditSnapshot() : null;
        $this->audit->record($entity, 'updated', $before, $after);
        $this->activities->record($entity, ActivityType::Updated, 'Authorization record updated');
        DomainEventRecorder::record(new RecordLifecycleEvent($entity->getEntityType(), 'updated', $entity->getEntityId(), ['before' => $before, 'after' => $after ?? []]));
    }

    public function onDeleted(PlatformEntityInterface $entity): void
    {
        $before = $entity instanceof AuditableInterface ? $entity->toAuditSnapshot() : null;
        $this->audit->record($entity, 'deleted', $before, null);
        $this->activities->record($entity, ActivityType::Deleted, 'Authorization record deleted');
        DomainEventRecorder::record(new RecordLifecycleEvent($entity->getEntityType(), 'deleted', $entity->getEntityId(), $before ?? []));
    }
}
