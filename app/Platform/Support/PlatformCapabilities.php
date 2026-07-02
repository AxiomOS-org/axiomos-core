<?php

declare(strict_types=1);

namespace App\Platform\Support;

/**
 * Canonical list of platform capabilities shipped with every ERP entity.
 */
final class PlatformCapabilities
{
    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            'uuid',
            'audit_trail',
            'activity_timeline',
            'attachments',
            'notes',
            'comments',
            'tags',
            'status',
            'labels',
            'favorites',
            'custom_fields',
            'approval_workflow',
            'notifications',
            'soft_delete',
            'version_history',
            'ai_context',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }

    public static function count(): int
    {
        return count(self::all());
    }
}

