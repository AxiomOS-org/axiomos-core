<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\Services;

use App\Platform\Activity\ActivityService;
use App\Platform\Activity\ActivityType;
use App\Platform\Audit\AuditTrailService;
use App\Platform\Comments\CommentService;
use App\Platform\Notes\NoteService;
use App\Platform\Support\AuditableInterface;
use App\Platform\Support\PlatformEntityInterface;
use Modules\Authentication\Application\Events\DomainEventRecorder;
use Modules\Authentication\Domain\Events\RecordLifecycleEvent;

final class AuthenticationPlatformHooks
{
    public function __construct(
        private readonly AuditTrailService $audit,
        private readonly ActivityService $activities,
        private readonly NoteService $notes,
        private readonly CommentService $comments,
    ) {
    }

    public function onCreated(PlatformEntityInterface $entity): void
    {
        $snapshot = $entity instanceof AuditableInterface ? $entity->toAuditSnapshot() : null;
        $this->audit->record($entity, 'created', null, $snapshot);
        $this->activities->record($entity, ActivityType::Created, 'Authentication record created');
        $this->notes->create($entity, 'Authentication lifecycle create hook executed.', 'Lifecycle');
        $this->comments->create($entity, 'System lifecycle marker.');
        DomainEventRecorder::record(new RecordLifecycleEvent($entity->getEntityType(), 'created', $entity->getEntityId(), $snapshot ?? []));
    }

    /**
     * @param array<string, mixed> $before
     */
    public function onUpdated(PlatformEntityInterface $entity, array $before): void
    {
        $after = $entity instanceof AuditableInterface ? $entity->toAuditSnapshot() : null;
        $this->audit->record($entity, 'updated', $before, $after);
        $this->activities->record($entity, ActivityType::Updated, 'Authentication record updated');
        DomainEventRecorder::record(new RecordLifecycleEvent($entity->getEntityType(), 'updated', $entity->getEntityId(), ['before' => $before]));
    }
}
