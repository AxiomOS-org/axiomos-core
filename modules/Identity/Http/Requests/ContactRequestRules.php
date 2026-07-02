<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Requests;

use Modules\Identity\Http\Support\EnterpriseScopeRequestRules;

final class ContactRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return array_merge([
            'identity_id' => ['required', 'uuid'],
            'contact_type' => ['required', 'string', 'max:64'],
            'value' => ['required', 'string', 'max:255'],
            'is_primary' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:active,inactive'],
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
            'contact_type' => ['sometimes', 'string', 'max:64'],
            'value' => ['sometimes', 'string', 'max:255'],
            'is_primary' => ['nullable', 'boolean'],
            'status' => ['sometimes', 'in:active,inactive'],
            'updated_by' => ['nullable', 'uuid'],
        ], EnterpriseScopeRequestRules::update());
    }
}
