<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Requests;

final class DeviceRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return [
            'identity_id' => ['required', 'uuid'],
            'device_type' => ['required', 'string', 'max:64'],
            'fingerprint' => ['required', 'string', 'max:255'],
            'user_agent' => ['nullable', 'string'],
            'last_seen_at' => ['nullable', 'date'],
            'status' => ['nullable', 'in:active,revoked'],
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
            'device_type' => ['sometimes', 'string', 'max:64'],
            'fingerprint' => ['sometimes', 'string', 'max:255'],
            'user_agent' => ['nullable', 'string'],
            'last_seen_at' => ['nullable', 'date'],
            'status' => ['sometimes', 'in:active,revoked'],
            'updated_by' => ['nullable', 'uuid'],
        ];
    }
}
