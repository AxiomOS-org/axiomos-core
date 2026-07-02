<?php

declare(strict_types=1);

namespace Modules\Authentication\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class AuthOauthClient extends PlatformEntityModel
{
    protected $table = 'auth_oauth_clients';

    /** @var list<string> */
    protected $fillable = [
        'client_id',
        'client_secret_hash',
        'name',
        'redirect_uris',
        'scopes',
        'revoked_at',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = ['redirect_uris' => 'array', 'scopes' => 'array', 'revoked_at' => 'datetime'];

    public static function entityType(): string
    {
        return 'authentication.oauth_client';
    }
}
