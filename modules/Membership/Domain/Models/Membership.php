<?php

declare(strict_types=1);

namespace Modules\Membership\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class Membership extends PlatformEntityModel
{
    protected $table = 'memberships';

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'organization_id',
        'membership_type',
        'status',
        'scopes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'scopes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'identity.membership';
    }
}
