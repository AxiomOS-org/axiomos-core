<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Requests;

final class TeamRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return [
            'organization_id' => ['required', 'uuid'],
            'code' => ['required', 'string', 'max:64'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'leader_identity_id' => ['nullable', 'uuid'],
            'status' => ['nullable', 'in:active,inactive'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function update(): array
    {
        return [
            'organization_id' => ['sometimes', 'uuid'],
            'code' => ['sometimes', 'string', 'max:64'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'leader_identity_id' => ['nullable', 'uuid'],
            'status' => ['sometimes', 'in:active,inactive'],
            'updated_by' => ['nullable', 'uuid'],
        ];
    }
}
