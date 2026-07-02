<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Requests;

final class ApiTokenRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return [
            'identity_id' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:120'],
            'token_hash' => ['required', 'string', 'max:255'],
            'scopes' => ['nullable', 'array'],
            'expires_at' => ['nullable', 'date'],
            'last_used_at' => ['nullable', 'date'],
            'status' => ['nullable', 'in:active,expired,revoked'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function update(): array
    {
        return [
            'identity_id' => ['sometimes', 'uuid'],
            'name' => ['sometimes', 'string', 'max:120'],
            'token_hash' => ['sometimes', 'string', 'max:255'],
            'scopes' => ['nullable', 'array'],
            'expires_at' => ['nullable', 'date'],
            'last_used_at' => ['nullable', 'date'],
            'status' => ['sometimes', 'in:active,expired,revoked'],
            'updated_by' => ['nullable', 'uuid'],
        ];
    }
}
