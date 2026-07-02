<?php

declare(strict_types=1);

namespace Modules\Authentication\Domain\Models;

use App\Platform\Support\PlatformEntityModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Users\Domain\Models\User;

final class AuthCredential extends PlatformEntityModel
{
    protected $table = 'auth_credentials';

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'password_hash',
        'email_verified_at',
        'failed_attempts',
        'locked_until',
        'password_changed_at',
        'must_change_password',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'failed_attempts' => 'integer',
        'must_change_password' => 'boolean',
        'email_verified_at' => 'datetime',
        'locked_until' => 'datetime',
        'password_changed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'authentication.credential';
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
