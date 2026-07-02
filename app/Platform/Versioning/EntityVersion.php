<?php

declare(strict_types=1);

namespace App\Platform\Versioning;

use App\Platform\Support\PlatformRecord;

final class EntityVersion extends PlatformRecord
{
    public const UPDATED_AT = null;

    protected $table = 'universal_entity_versions';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'entity_type',
        'entity_id',
        'version_number',
        'snapshot',
        'created_by',
        'created_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'snapshot' => 'array',
        'created_at' => 'datetime',
    ];
}
