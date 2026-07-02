<?php

declare(strict_types=1);

namespace Modules\Authentication\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class AuthSecurityEvent extends PlatformEntityModel
{
    protected $table = 'auth_security_events';

    /** @var list<string> */
    protected $fillable = [
        'event_type',
        'user_id',
        'ip_address',
        'user_agent',
        'geo',
        'metadata',
        'severity',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = ['geo' => 'array', 'metadata' => 'array'];

    public static function entityType(): string
    {
        return 'authentication.security_event';
    }
}
