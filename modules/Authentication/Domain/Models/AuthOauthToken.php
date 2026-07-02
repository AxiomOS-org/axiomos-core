<?php

declare(strict_types=1);

namespace Modules\Authentication\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class AuthOauthToken extends PlatformEntityModel
{
    protected $table = 'auth_oauth_tokens';

    /** @var list<string> */
    protected $fillable = [
        'oauth_client_id',
        'user_id',
        'access_token_hash',
        'refresh_token_hash',
        'scopes',
        'expires_at',
        'revoked_at',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = ['scopes' => 'array', 'expires_at' => 'datetime', 'revoked_at' => 'datetime'];

    public static function entityType(): string
    {
        return 'authentication.oauth_token';
    }
}
