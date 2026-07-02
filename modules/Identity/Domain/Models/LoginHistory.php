<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Models;

use App\Platform\Support\PlatformEntityModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LoginHistory extends PlatformEntityModel
{
    protected $table = 'login_history';

    /** @var list<string> */
    protected $fillable = [
        'identity_id',
        'user_id',
        'organization_id',
        'company_id',
        'branch_id',
        'department_id',
        'ip_address',
        'user_agent',
        'success',
        'logged_at',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'success' => 'boolean',
        'logged_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'identity.login_history';
    }

    /** @return BelongsTo<Identity, $this> */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(Identity::class);
    }
}
