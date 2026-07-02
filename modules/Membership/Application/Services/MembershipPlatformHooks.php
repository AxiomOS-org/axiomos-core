<?php

declare(strict_types=1);

namespace Modules\Membership\Application\Services;

use App\Platform\Activity\ActivityService;
use App\Platform\Activity\ActivityType;
use App\Platform\AI\AiContextService;
use App\Platform\Attachments\AttachmentService;
use App\Platform\Audit\AuditTrailService;
use App\Platform\Comments\CommentService;
use App\Platform\Notes\NoteService;
use App\Platform\Notifications\NotificationService;
use App\Platform\Support\AuditableInterface;
use App\Platform\Support\PlatformEntityInterface;
use App\Platform\Support\VersionableInterface;
use App\Platform\Tags\TagService;
use App\Platform\Versioning\VersionHistoryService;
use Modules\Membership\Application\Events\DomainEventRecorder;
use Modules\Membership\Domain\Events\RecordLifecycleEvent;

final class MembershipPlatformHooks
{
    public function __construct(
        private readonly AuditTrailService $audit,
        private readonly ActivityService $activities,
        private readonly AiContextService $aiContext,
        private readonly NotificationService $notifications,
        private readonly VersionHistoryService $versions,
        private readonly NoteService $notes,
        private readonly CommentService $comments,
        private readonly AttachmentService $attachments,
        private readonly TagService $tags,
    ) {
    }

    public function onCreated(PlatformEntityInterface $entity): void
    {
        $snapshot = $entity instanceof AuditableInterface ? $entity->toAuditSnapshot() : null;
        $this->audit->record($entity, 'created', null, $snapshot);
        $this->activities->record($entity, ActivityType::Created, 'Record created', null, null, ['entity_type' => $entity->getEntityType()]);
        $this->aiContext->upsert($entity, 'default', ['entity_type' => $entity->getEntityType(), 'summary' => 'Membership record']);
        $this->notifications->create(userId: null, entity: $entity, title: 'Record created', body: sprintf('%s was created.', $entity->getEntityType()));

        if ($entity instanceof VersionableInterface) {
            $this->versions->recordVersion($entity, $entity->toVersionSnapshot());
        }

        $this->notes->create($entity, 'Membership record provisioned.', 'Lifecycle');
        $this->comments->create($entity, 'System lifecycle comment for audit trail.');
        $this->attachments->create($entity, ['disk' => 'local', 'path' => 'memberships/' . $entity->getEntityId() . '/manifest.json', 'filename' => 'manifest.json', 'mime_type' => 'application/json', 'size_bytes' => 2]);
        $this->tags->attach($entity, 'membership', scope: 'identity');
        DomainEventRecorder::record(new RecordLifecycleEvent($entity->getEntityType(), 'created', $entity->getEntityId(), $snapshot ?? []));
    }

    /** @param array<string, mixed> $before */
    public function onUpdated(PlatformEntityInterface $entity, array $before): void
    {
        $after = $entity instanceof AuditableInterface ? $entity->toAuditSnapshot() : null;
        $this->audit->record($entity, 'updated', $before, $after);
        $this->activities->record($entity, ActivityType::Updated, 'Record updated');
        if ($entity instanceof VersionableInterface) {
            $this->versions->recordVersion($entity, $entity->toVersionSnapshot());
        }
        DomainEventRecorder::record(new RecordLifecycleEvent($entity->getEntityType(), 'updated', $entity->getEntityId(), ['before' => $before]));
    }

    public function onDeleted(PlatformEntityInterface $entity): void
    {
        $before = $entity instanceof AuditableInterface ? $entity->toAuditSnapshot() : null;
        $this->audit->record($entity, 'deleted', $before, null);
        $this->activities->record($entity, ActivityType::Deleted, 'Record deleted');
        DomainEventRecorder::record(new RecordLifecycleEvent($entity->getEntityType(), 'deleted', $entity->getEntityId(), $before ?? []));
    }
}
