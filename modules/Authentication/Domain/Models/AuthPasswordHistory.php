<?php

declare(strict_types=1);

namespace Modules\Authentication\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class AuthPasswordHistory extends PlatformEntityModel
{
    protected $table = 'auth_password_history';

    /** @var list<string> */
    protected $fillable = ['user_id', 'password_hash', 'created_by', 'updated_by', 'deleted_by'];

    public static function entityType(): string
    {
        return 'authentication.password_history';
    }
}
