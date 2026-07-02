<?php

declare(strict_types=1);

namespace Modules\Authorization\Domain\Models;

use App\Platform\Support\PlatformEntityModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class AuthorizationRoleAssignment extends PlatformEntityModel
{
    protected $table = 'authorization_role_assignments';

    /** @var list<string> */
    protected $fillable = [
        'role_id',
        'assignable_type',
        'assignable_id',
        'organization_id',
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
        return 'security.authorization_role_assignment';
    }

    /** @return BelongsTo<AuthorizationRole, $this> */
    public function role(): BelongsTo
    {
        return $this->belongsTo(AuthorizationRole::class, 'role_id');
    }

    /** @return MorphTo<\Illuminate\Database\Eloquent\Model, $this> */
    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }
}
