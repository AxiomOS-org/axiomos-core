<?php

declare(strict_types=1);

namespace Modules\Authorization\Http\Requests;

final class PermissionRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return [
            'slug' => ['required', 'string', 'max:160'],
            'name' => ['required', 'string', 'max:180'],
            'module' => ['required', 'string', 'max:80'],
            'action' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string'],
            'is_system' => ['boolean'],
            'status' => ['nullable', 'string', 'max:32'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function update(): array
    {
        return [
            'slug' => ['sometimes', 'string', 'max:160'],
            'name' => ['sometimes', 'string', 'max:180'],
            'module' => ['sometimes', 'string', 'max:80'],
            'action' => ['sometimes', 'string', 'max:80'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_system' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'string', 'max:32'],
            'updated_by' => ['nullable', 'uuid'],
        ];
    }
}
