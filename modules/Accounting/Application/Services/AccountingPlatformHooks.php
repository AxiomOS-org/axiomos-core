<?php
declare(strict_types=1);
namespace Modules\Accounting\Application\Services;
use App\Platform\Activity\ActivityService;
use App\Platform\Activity\ActivityType;
use App\Platform\AI\AiContextService;
use App\Platform\Audit\AuditTrailService;
use App\Platform\Notifications\NotificationService;
use App\Platform\Support\AuditableInterface;
use App\Platform\Support\PlatformEntityInterface;
use App\Platform\Support\VersionableInterface;
use App\Platform\Versioning\VersionHistoryService;
final class AccountingPlatformHooks {
    public function __construct(private readonly AuditTrailService $audit, private readonly ActivityService $activities, private readonly AiContextService $aiContext, private readonly NotificationService $notifications, private readonly VersionHistoryService $versions) {}
    public function onCreated(PlatformEntityInterface $entity): void { $this->audit->record($entity,'created',null,$entity instanceof AuditableInterface ? $entity->toAuditSnapshot() : null); $this->activities->record($entity,ActivityType::Created,'Accounting record created'); $this->aiContext->upsert($entity,'default',['entity_type'=>$entity->getEntityType(),'module'=>'Accounting']); $this->notifications->create(userId: null, entity: $entity, title: 'Accounting record created', body: sprintf('%s created.', $entity->getEntityType())); if ($entity instanceof VersionableInterface) { $this->versions->recordVersion($entity,$entity->toVersionSnapshot()); } }
    public function onUpdated(PlatformEntityInterface $entity, array $before): void { $this->audit->record($entity,'updated',$before,$entity instanceof AuditableInterface ? $entity->toAuditSnapshot() : null); $this->activities->record($entity,ActivityType::Updated,'Accounting record updated'); if ($entity instanceof VersionableInterface) { $this->versions->recordVersion($entity,$entity->toVersionSnapshot()); } }
    public function onDeleted(PlatformEntityInterface $entity): void { $this->audit->record($entity,'deleted',$entity instanceof AuditableInterface ? $entity->toAuditSnapshot() : null,null); $this->activities->record($entity,ActivityType::Deleted,'Accounting record deleted'); }
}

