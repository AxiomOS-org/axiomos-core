<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Requests;

final class PasswordResetRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function forgot(): array
    {
        return ['email' => ['required', 'email']];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function reset(): array
    {
        return [
            'token' => ['required', 'string', 'min:16'],
            'new_password' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }
}
