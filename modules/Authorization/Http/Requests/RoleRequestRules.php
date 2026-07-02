<?php

declare(strict_types=1);

namespace Modules\Authorization\Http\Requests;

final class RoleRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return [
            'organization_id' => ['nullable', 'uuid'],
            'slug' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string'],
            'is_system' => ['boolean'],
            'status' => ['nullable', 'string', 'max:32'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['uuid'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function update(): array
    {
        return [
            'organization_id' => ['sometimes', 'nullable', 'uuid'],
            'slug' => ['sometimes', 'string', 'max:120'],
            'name' => ['sometimes', 'string', 'max:160'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_system' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'string', 'max:32'],
            'permission_ids' => ['sometimes', 'array'],
            'permission_ids.*' => ['uuid'],
            'updated_by' => ['nullable', 'uuid'],
        ];
    }
}
