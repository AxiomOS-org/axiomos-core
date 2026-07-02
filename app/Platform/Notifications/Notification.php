<?php

declare(strict_types=1);

namespace App\Platform\Notifications;

use App\Platform\Support\PlatformRecord;

final class Notification extends PlatformRecord
{
    protected $table = 'universal_notifications';

    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
        'channel',
        'title',
        'body',
        'payload',
        'read_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'payload' => 'array',
        'read_at' => 'datetime',
    ];
}

