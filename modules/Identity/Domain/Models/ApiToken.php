<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Models;

use App\Platform\Support\PlatformEntityModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ApiToken extends PlatformEntityModel
{
    protected $table = 'api_tokens';

    /** @var list<string> */
    protected $fillable = [
        'identity_id',
        'organization_id',
        'company_id',
        'branch_id',
        'department_id',
        'name',
        'token_hash',
        'scopes',
        'expires_at',
        'last_used_at',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'scopes' => 'array',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'identity.api_token';
    }

    /** @return BelongsTo<Identity, $this> */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(Identity::class);
    }
}
