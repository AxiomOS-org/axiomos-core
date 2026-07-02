<?php

declare(strict_types=1);

namespace Modules\Authentication\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class AuthPasswordReset extends PlatformEntityModel
{
    protected $table = 'auth_password_resets';

    /** @var list<string> */
    protected $fillable = ['user_id', 'token_hash', 'expires_at', 'used_at', 'status', 'created_by', 'updated_by', 'deleted_by'];

    /** @var array<string, string> */
    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'authentication.password_reset';
    }
}
