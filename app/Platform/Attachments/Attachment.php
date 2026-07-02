<?php

declare(strict_types=1);

namespace App\Platform\Attachments;

use App\Platform\Support\PlatformRecord;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Attachment extends PlatformRecord
{
    use SoftDeletes;

    protected $table = 'universal_attachments';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'disk',
        'path',
        'filename',
        'mime_type',
        'size_bytes',
        'metadata',
        'uploaded_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'metadata' => 'array',
        'size_bytes' => 'integer',
    ];
}

