<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Requests;

final class PasswordChangeRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function change(): array
    {
        return [
            'user_id' => ['required', 'uuid'],
            'current_password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'string', 'min:8', 'max:255'],
            'organization_id' => ['nullable', 'uuid'],
        ];
    }
}
