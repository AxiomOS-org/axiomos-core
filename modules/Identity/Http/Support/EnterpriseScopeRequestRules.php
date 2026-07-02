<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Support;

/**
 * Shared enterprise scope validation for Identity platform entities.
 */
final class EnterpriseScopeRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return [
            'organization_id' => ['nullable', 'uuid'],
            'company_id' => ['nullable', 'uuid'],
            'branch_id' => ['nullable', 'uuid'],
            'department_id' => ['nullable', 'uuid'],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function update(): array
    {
        return [
            'organization_id' => ['sometimes', 'uuid'],
            'company_id' => ['sometimes', 'uuid'],
            'branch_id' => ['sometimes', 'uuid'],
            'department_id' => ['sometimes', 'uuid'],
        ];
    }
}
