<?php

declare(strict_types=1);

namespace App\Platform\Labels;

use App\Platform\Support\PlatformRecord;

final class Label extends PlatformRecord
{
    protected $table = 'universal_labels';

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

