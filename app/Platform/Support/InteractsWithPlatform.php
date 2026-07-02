<?php

declare(strict_types=1);

namespace App\Platform\Support;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Polymorphic relationships to the platform layer.
 *
 * Note: core tests do not exercise these relationships; this trait exists to
 * provide a stable API surface for future platform integration.
 */
trait InteractsWithPlatform
{
    abstract public function getEntityType(): string;

    abstract public function getEntityId(): string;

    public function auditLogs(): MorphMany
    {
        /** @var class-string $class */
        $class = \App\Platform\Audit\AuditLog::class;
        return $this->morphMany($class, 'entity', 'entity_type', 'entity_id');
    }

    public function activities(): MorphMany
    {
        $class = \App\Platform\Activity\Activity::class;
        return $this->morphMany($class, 'entity', 'entity_type', 'entity_id');
    }

    public function attachments(): MorphMany
    {
        $class = \App\Platform\Attachments\Attachment::class;
        return $this->morphMany($class, 'entity', 'entity_type', 'entity_id');
    }

    public function notes(): MorphMany
    {
        $class = \App\Platform\Notes\Note::class;
        return $this->morphMany($class, 'entity', 'entity_type', 'entity_id');
    }

    public function comments(): MorphMany
    {
        $class = \App\Platform\Comments\Comment::class;
        return $this->morphMany($class, 'entity', 'entity_type', 'entity_id');
    }

    public function favorites(): MorphMany
    {
        $class = \App\Platform\Tags\Favorite::class;
        return $this->morphMany($class, 'entity', 'entity_type', 'entity_id');
    }

    public function notifications(): MorphMany
    {
        $class = \App\Platform\Notifications\Notification::class;
        return $this->morphMany($class, 'entity', 'entity_type', 'entity_id');
    }

    public function versions(): MorphMany
    {
        $class = \App\Platform\Versioning\EntityVersion::class;
        return $this->morphMany($class, 'entity', 'entity_type', 'entity_id');
    }

    public function aiContexts(): MorphMany
    {
        $class = \App\Platform\AI\AiContext::class;
        return $this->morphMany($class, 'entity', 'entity_type', 'entity_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Platform\Tags\Tag::class,
            'universal_taggables',
            'entity_id',
            'tag_id',
        )
            ->wherePivot('entity_type', $this->getEntityType())
            ->withTimestamps();
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Platform\Labels\Label::class,
            'universal_labelables',
            'entity_id',
            'label_id',
        )
            ->wherePivot('entity_type', $this->getEntityType())
            ->withTimestamps();
    }

    public function approvalRequests(): MorphMany
    {
        $class = \App\Platform\Approval\ApprovalRequest::class;
        return $this->morphMany($class, 'entity', 'entity_type', 'entity_id');
    }
}

