<?php

declare(strict_types=1);

namespace Modules\Authorization\Domain\Models;

use App\Platform\Support\PlatformEntityModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AuthorizationRolePermission extends PlatformEntityModel
{
    protected $table = 'authorization_role_permissions';

    /** @var list<string> */
    protected $fillable = [
        'role_id',
        'permission_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'security.authorization_role_permission';
    }

    /** @return BelongsTo<AuthorizationRole, $this> */
    public function role(): BelongsTo
    {
        return $this->belongsTo(AuthorizationRole::class, 'role_id');
    }

    /** @return BelongsTo<AuthorizationPermission, $this> */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(AuthorizationPermission::class, 'permission_id');
    }
}
