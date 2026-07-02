<?php

declare(strict_types=1);

namespace App\Platform\Attachments;

use App\Platform\Support\PlatformEntityInterface;

final class AttachmentService
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function create(
        PlatformEntityInterface $entity,
        array $attributes,
        ?string $uploadedBy = null,
    ): Attachment {
        $attachment = new Attachment();
        $attachment->entity_type = $entity->getEntityType();
        $attachment->entity_id = $entity->getEntityId();
        $attachment->disk = (string) ($attributes['disk'] ?? 'local');
        $attachment->path = (string) ($attributes['path'] ?? '');
        $attachment->filename = (string) ($attributes['filename'] ?? '');
        $attachment->mime_type = $attributes['mime_type'] ?? null;
        $attachment->size_bytes = (int) ($attributes['size_bytes'] ?? 0);
        $attachment->metadata = $attributes['metadata'] ?? null;
        $attachment->uploaded_by = $uploadedBy;
        $attachment->save();

        return $attachment;
    }

    /**
     * @return array<int, Attachment>
     */
    public function list(PlatformEntityInterface $entity, int $limit = 50): array
    {
        return Attachment::query()
            ->where('entity_type', $entity->getEntityType())
            ->where('entity_id', $entity->getEntityId())
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->all();
    }
}

