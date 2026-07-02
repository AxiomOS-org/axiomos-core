<?php

declare(strict_types=1);

namespace Modules\Users\Http\Requests;

use Modules\Identity\Http\Support\EnterpriseScopeRequestRules;

final class UserRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return array_merge([
            'identity_id' => ['required', 'uuid'],
            'username' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'display_name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:32'],
            'settings' => ['nullable', 'array'],
            'created_by' => ['nullable', 'uuid'],
        ], EnterpriseScopeRequestRules::create());
    }

    /**
     * @return array<string, list<string>>
     */
    public static function update(): array
    {
        return array_merge([
            'identity_id' => ['sometimes', 'uuid'],
            'username' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email', 'max:255'],
            'display_name' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'max:32'],
            'settings' => ['nullable', 'array'],
            'updated_by' => ['nullable', 'uuid'],
        ], EnterpriseScopeRequestRules::update());
    }
}
