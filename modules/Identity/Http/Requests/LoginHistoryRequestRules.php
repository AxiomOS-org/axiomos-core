<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Requests;

final class LoginHistoryRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return [
            'identity_id' => ['required', 'uuid'],
            'user_id' => ['nullable', 'uuid'],
            'ip_address' => ['nullable', 'string', 'max:64'],
            'user_agent' => ['nullable', 'string'],
            'success' => ['required', 'boolean'],
            'logged_at' => ['required', 'date'],
            'status' => ['nullable', 'in:success,failed'],
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
            'user_id' => ['nullable', 'uuid'],
            'ip_address' => ['nullable', 'string', 'max:64'],
            'user_agent' => ['nullable', 'string'],
            'success' => ['sometimes', 'boolean'],
            'logged_at' => ['sometimes', 'date'],
            'status' => ['sometimes', 'in:success,failed'],
            'updated_by' => ['nullable', 'uuid'],
        ];
    }
}
