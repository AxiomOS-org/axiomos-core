<?php

declare(strict_types=1);

namespace Modules\Users\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class User extends PlatformEntityModel
{
    protected $table = 'users';

    /** @var list<string> */
    protected $fillable = [
        'identity_id',
        'username',
        'email',
        'display_name',
        'status',
        'settings',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'identity.user';
    }

}
