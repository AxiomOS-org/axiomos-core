<?php

declare(strict_types=1);

namespace Modules\Authentication\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class AuthEmailVerification extends PlatformEntityModel
{
    protected $table = 'auth_email_verifications';

    /** @var list<string> */
    protected $fillable = ['user_id', 'token_hash', 'expires_at', 'verified_at', 'status', 'created_by', 'updated_by', 'deleted_by'];

    /** @var array<string, string> */
    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'authentication.email_verification';
    }
}
