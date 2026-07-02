<?php

declare(strict_types=1);

namespace App\Platform\Audit;

use App\Platform\Support\PlatformEntityInterface;
use Illuminate\Support\Carbon;

final class AuditTrailService
{
    public function record(
        PlatformEntityInterface $entity,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null,
        ?string $actorId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): AuditLog {
        $log = new AuditLog();
        $log->entity_type = $entity->getEntityType();
        $log->entity_id = $entity->getEntityId();
        $log->action = $action;
        $log->old_values = $oldValues;
        $log->new_values = $newValues;
        $log->metadata = $metadata;
        $log->actor_id = $actorId;
        $log->ip_address = $ipAddress;
        $log->user_agent = $userAgent;
        $log->occurred_at = Carbon::now();
        $log->save();

        return $log;
    }
}

