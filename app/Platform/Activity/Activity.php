<?php

declare(strict_types=1);

namespace App\Platform\Activity;

use App\Platform\Support\PlatformRecord;

final class Activity extends PlatformRecord
{
    protected $table = 'universal_activities';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'entity_type',
        'entity_id',
        'type',
        'title',
        'description',
        'metadata',
        'actor_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
    ];
}

