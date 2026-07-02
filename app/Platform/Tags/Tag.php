<?php

declare(strict_types=1);

namespace App\Platform\Tags;

use App\Platform\Support\PlatformRecord;

final class Tag extends PlatformRecord
{
    protected $table = 'universal_tags';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'color',
        'scope',
        'created_by',
    ];
}

