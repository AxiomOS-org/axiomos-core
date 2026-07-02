<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Requests;

final class TeamMemberRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return [
            'team_id' => ['required', 'uuid'],
            'identity_id' => ['required', 'uuid'],
            'role' => ['nullable', 'string', 'max:64'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function update(): array
    {
        return [
            'team_id' => ['sometimes', 'uuid'],
            'identity_id' => ['sometimes', 'uuid'],
            'role' => ['sometimes', 'string', 'max:64'],
            'updated_by' => ['nullable', 'uuid'],
        ];
    }
}