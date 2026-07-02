<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Requests;

final class IdentitySessionRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return [
            'identity_id' => ['required', 'uuid'],
            'session_token_hash' => ['required', 'string', 'max:255'],
            'ip_address' => ['nullable', 'string', 'max:64'],
            'user_agent' => ['nullable', 'string'],
            'started_at' => ['required', 'date'],
            'expires_at' => ['nullable', 'date'],
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
            'session_token_hash' => ['sometimes', 'string', 'max:255'],
            'ip_address' => ['nullable', 'string', 'max:64'],
            'user_agent' => ['nullable', 'string'],
            'started_at' => ['sometimes', 'date'],
            'expires_at' => ['nullable', 'date'],
            'status' => ['sometimes', 'in:active,expired,revoked'],
            'updated_by' => ['nullable', 'uuid'],
        ];
    }
}
