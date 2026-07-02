<?php

declare(strict_types=1);

namespace Modules\Authentication\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class AuthPasswordPolicy extends PlatformEntityModel
{
    protected $table = 'auth_password_policies';

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'rules',
        'min_length',
        'expiry_days',
        'history_count',
        'lockout_threshold',
        'lockout_minutes',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'rules' => 'array',
        'min_length' => 'integer',
        'expiry_days' => 'integer',
        'history_count' => 'integer',
        'lockout_threshold' => 'integer',
        'lockout_minutes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'authentication.password_policy';
    }
}
