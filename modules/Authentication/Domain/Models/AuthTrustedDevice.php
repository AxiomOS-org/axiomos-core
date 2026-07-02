<?php

declare(strict_types=1);

namespace Modules\Authentication\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class AuthTrustedDevice extends PlatformEntityModel
{
    protected $table = 'auth_trusted_devices';

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'device_fingerprint',
        'user_agent',
        'ip_address',
        'geo',
        'trusted_until',
        'last_used_at',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'geo' => 'array',
        'trusted_until' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'authentication.trusted_device';
    }
}
