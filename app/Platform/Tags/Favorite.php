<?php

declare(strict_types=1);

namespace App\Platform\Tags;

use App\Platform\Support\PlatformRecord;

final class Favorite extends PlatformRecord
{
    protected $table = 'universal_favorites';

    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
    ];
}

