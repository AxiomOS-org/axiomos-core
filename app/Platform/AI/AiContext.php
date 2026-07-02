<?php

declare(strict_types=1);

namespace App\Platform\AI;

use App\Platform\Support\PlatformRecord;

final class AiContext extends PlatformRecord
{
    protected $table = 'universal_ai_contexts';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'context_key',
        'context',
        'metadata',
        'updated_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'context' => 'array',
        'metadata' => 'array',
    ];
}

