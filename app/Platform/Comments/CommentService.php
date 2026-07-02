<?php

declare(strict_types=1);

namespace App\Platform\Comments;

use App\Platform\Support\PlatformEntityInterface;

final class CommentService
{
    public function create(
        PlatformEntityInterface $entity,
        string $body,
        ?string $actorId = null,
        ?string $parentId = null,
    ): Comment {
        $comment = new Comment();
        $comment->entity_type = $entity->getEntityType();
        $comment->entity_id = $entity->getEntityId();
        $comment->body = $body;
        $comment->created_by = $actorId;
        $comment->updated_by = $actorId;
        $comment->parent_id = $parentId;
        $comment->save();

        return $comment;
    }
}

