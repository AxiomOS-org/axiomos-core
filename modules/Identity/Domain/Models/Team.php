<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Models;

use App\Platform\Support\PlatformEntityModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Team extends PlatformEntityModel
{
    protected $table = 'teams';

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'company_id',
        'branch_id',
        'department_id',
        'code',
        'name',
        'description',
        'leader_identity_id',
        'status',
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
        return 'identity.team';
    }

    /** @return BelongsTo<Identity, $this> */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(Identity::class, 'leader_identity_id');
    }

    /** @return HasMany<TeamMember, $this> */
    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }
}
