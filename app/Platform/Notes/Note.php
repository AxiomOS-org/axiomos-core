<?php

declare(strict_types=1);

namespace App\Platform\Notes;

use App\Platform\Support\PlatformRecord;

final class Note extends PlatformRecord
{
    protected $table = 'universal_notes';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'title',
        'body',
        'is_pinned',
        'created_by',
        'updated_by',
    ];
}

