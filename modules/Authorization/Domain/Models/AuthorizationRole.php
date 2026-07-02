<?php

declare(strict_types=1);

namespace Modules\Authorization\Domain\Models;

use App\Platform\Support\PlatformEntityModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class AuthorizationRole extends PlatformEntityModel
{
    protected $table = 'authorization_roles';

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'slug',
        'name',
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
        return 'security.authorization_role';
    }

    /** @return BelongsToMany<AuthorizationPermission, $this> */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            AuthorizationPermission::class,
            'authorization_role_permissions',
            'role_id',
            'permission_id'
        );
    }

    /** @return HasMany<AuthorizationRoleAssignment, $this> */
    public function assignments(): HasMany
    {
        return $this->hasMany(AuthorizationRoleAssignment::class, 'role_id');
    }
}
