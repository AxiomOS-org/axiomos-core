<?php

declare(strict_types=1);

namespace Modules\Authorization\Http\Requests;

final class RoleAssignmentRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function assign(): array
    {
        return [
            'assignable_type' => ['nullable', 'string', 'max:180'],
            'assignable_id' => ['required', 'uuid'],
            'organization_id' => ['nullable', 'uuid'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function revoke(): array
    {
        return [
            'assignable_type' => ['nullable', 'string', 'max:180'],
            'assignable_id' => ['required', 'uuid'],
            'organization_id' => ['nullable', 'uuid'],
            'updated_by' => ['nullable', 'uuid'],
        ];
    }
}
