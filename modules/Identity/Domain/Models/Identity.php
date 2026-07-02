<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Models;

use App\Platform\Support\PlatformEntityModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Identity extends PlatformEntityModel
{
    protected $table = 'identities';

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'company_id',
        'branch_id',
        'identity_type',
        'code',
        'display_name',
        'legal_name',
        'email',
        'phone',
        'status',
        'metadata',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'identity.identity';
    }

    /** @return HasMany<Team, $this> */
    public function teamsLed(): HasMany
    {
        return $this->hasMany(Team::class, 'leader_identity_id');
    }
}
