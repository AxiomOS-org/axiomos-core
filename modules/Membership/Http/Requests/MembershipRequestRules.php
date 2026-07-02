<?php

declare(strict_types=1);

namespace Modules\Membership\Http\Requests;

use Modules\Identity\Http\Support\EnterpriseScopeRequestRules;

final class MembershipRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return array_merge([
            'user_id' => ['required', 'uuid'],
            'organization_id' => ['required', 'uuid'],
            'membership_type' => ['required', 'in:owner,admin,member,guest'],
            'status' => ['nullable', 'string', 'max:32'],
            'scopes' => ['nullable', 'array'],
            'created_by' => ['nullable', 'uuid'],
        ], EnterpriseScopeRequestRules::create());
    }

    /**
     * @return array<string, list<string>>
     */
    public static function update(): array
    {
        return array_merge([
            'user_id' => ['sometimes', 'uuid'],
            'organization_id' => ['sometimes', 'uuid'],
            'membership_type' => ['sometimes', 'in:owner,admin,member,guest'],
            'status' => ['sometimes', 'string', 'max:32'],
            'scopes' => ['nullable', 'array'],
            'updated_by' => ['nullable', 'uuid'],
        ], EnterpriseScopeRequestRules::update());
    }
}
