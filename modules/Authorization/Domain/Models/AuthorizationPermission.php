<?php

declare(strict_types=1);

namespace Modules\Authorization\Domain\Models;

use App\Platform\Support\PlatformEntityModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class AuthorizationPermission extends PlatformEntityModel
{
    protected $table = 'authorization_permissions';

    /** @var list<string> */
    protected $fillable = [
        'slug',
        'name',
        'module',
        'action',
        'description',
        'is_system',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'is_system' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'security.authorization_permission';
    }

    /** @return BelongsToMany<AuthorizationRole, $this> */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            AuthorizationRole::class,
            'authorization_role_permissions',
            'permission_id',
            'role_id'
        );
    }
}
