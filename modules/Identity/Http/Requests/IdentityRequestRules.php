<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Requests;

final class IdentityRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return [
            'organization_id' => ['nullable', 'uuid'],
            'identity_type' => ['required', 'string', 'max:64'],
            'code' => ['required', 'string', 'max:64'],
            'display_name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'status' => ['nullable', 'in:active,inactive,suspended,blocked'],
            'metadata' => ['nullable', 'array'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function update(): array
    {
        return [
            'organization_id' => ['nullable', 'uuid'],
            'identity_type' => ['sometimes', 'string', 'max:64'],
            'code' => ['sometimes', 'string', 'max:64'],
            'display_name' => ['sometimes', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'status' => ['sometimes', 'in:active,inactive,suspended,blocked'],
            'metadata' => ['nullable', 'array'],
            'updated_by' => ['nullable', 'uuid'],
        ];
    }
}
