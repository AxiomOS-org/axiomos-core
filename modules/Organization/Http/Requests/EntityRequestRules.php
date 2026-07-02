<?php

declare(strict_types=1);

namespace Modules\Organization\Http\Requests;

use Modules\Organization\Domain\Enums\EntityStatus;

final class EntityRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return [
            'code' => ['required', 'string', 'max:64'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:2048'],
            'status' => ['nullable', 'in:' . implode(',', EntityStatus::values())],
            'timezone' => ['nullable', 'string', 'max:64'],
            'currency' => ['nullable', 'string', 'max:8'],
            'locale' => ['nullable', 'string', 'max:16'],
            'country' => ['nullable', 'string', 'max:8'],
            'settings' => ['nullable', 'array'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function update(): array
    {
        return [
            'code' => ['sometimes', 'string', 'max:64'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:2048'],
            'status' => ['sometimes', 'in:' . implode(',', EntityStatus::values())],
            'timezone' => ['sometimes', 'string', 'max:64'],
            'currency' => ['sometimes', 'string', 'max:8'],
            'locale' => ['sometimes', 'string', 'max:16'],
            'country' => ['sometimes', 'string', 'max:8'],
            'settings' => ['nullable', 'array'],
            'updated_by' => ['nullable', 'uuid'],
        ];
    }
}
