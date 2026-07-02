<?php

declare(strict_types=1);

namespace App\Platform\Notes;

use App\Platform\Support\PlatformEntityInterface;

final class NoteService
{
    public function create(
        PlatformEntityInterface $entity,
        string $body,
        ?string $title = null,
        bool $isPinned = false,
        ?string $actorId = null,
    ): Note {
        $note = new Note();
        $note->entity_type = $entity->getEntityType();
        $note->entity_id = $entity->getEntityId();
        $note->title = $title;
        $note->body = $body;
        $note->is_pinned = $isPinned;
        $note->created_by = $actorId;
        $note->updated_by = $actorId;
        $note->save();

        return $note;
    }
}

