<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Requests;

final class PersonalAccessTokenRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function issue(): array
    {
        return [
            'user_id' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:120'],
            'scopes' => ['nullable', 'array'],
        ];
    }
}
