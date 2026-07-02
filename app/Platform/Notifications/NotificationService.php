<?php

declare(strict_types=1);

namespace App\Platform\Notifications;

use App\Platform\Support\PlatformEntityInterface;

final class NotificationService
{
    public function create(
        ?string $userId,
        ?PlatformEntityInterface $entity,
        string $title,
        ?string $body = null,
        string $channel = 'in_app',
        ?array $payload = null,
    ): Notification {
        $notification = new Notification();
        $notification->user_id = $userId;
        $notification->entity_type = $entity?->getEntityType();
        $notification->entity_id = $entity?->getEntityId();
        $notification->channel = $channel;
        $notification->title = $title;
        $notification->body = $body;
        $notification->payload = $payload;
        $notification->save();

        return $notification;
    }
}

