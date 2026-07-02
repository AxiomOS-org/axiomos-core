<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Models;

use App\Platform\Support\PlatformEntityModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class IdentitySession extends PlatformEntityModel
{
    protected $table = 'identity_sessions';

    /** @var list<string> */
    protected $fillable = [
        'identity_id',
        'organization_id',
        'company_id',
        'branch_id',
        'department_id',
        'session_token_hash',
        'ip_address',
        'user_agent',
        'started_at',
        'expires_at',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'identity.session';
    }

    /** @return BelongsTo<Identity, $this> */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(Identity::class);
    }
}
