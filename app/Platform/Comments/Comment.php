<?php

declare(strict_types=1);

namespace App\Platform\Comments;

use App\Platform\Support\PlatformRecord;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Comment extends PlatformRecord
{
    use SoftDeletes;

    protected $table = 'universal_comments';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'parent_id',
        'body',
        'created_by',
        'updated_by',
    ];
}

