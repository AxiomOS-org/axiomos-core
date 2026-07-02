<?php

declare(strict_types=1);

namespace Modules\Authentication\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class AuthMfaMethod extends PlatformEntityModel
{
    protected $table = 'auth_mfa_methods';

    /** @var list<string> */
    protected $fillable = ['user_id', 'method_type', 'secret_encrypted', 'enabled', 'recovery_codes', 'status', 'created_by', 'updated_by', 'deleted_by'];

    /** @var array<string, string> */
    protected $casts = ['enabled' => 'boolean', 'recovery_codes' => 'array'];

    public static function entityType(): string
    {
        return 'authentication.mfa_method';
    }
}
