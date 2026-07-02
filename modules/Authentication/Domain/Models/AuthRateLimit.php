<?php

declare(strict_types=1);

namespace Modules\Authentication\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class AuthRateLimit extends PlatformEntityModel
{
    protected $table = 'auth_rate_limits';

    /** @var list<string> */
    protected $fillable = ['rate_key', 'attempts', 'window_start', 'blocked_until', 'status', 'created_by', 'updated_by', 'deleted_by'];

    /** @var array<string, string> */
    protected $casts = ['attempts' => 'integer', 'window_start' => 'datetime', 'blocked_until' => 'datetime'];

    public static function entityType(): string
    {
        return 'authentication.rate_limit';
    }
}
