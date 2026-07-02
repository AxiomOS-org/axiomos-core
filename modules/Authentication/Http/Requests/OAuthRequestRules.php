<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Requests;

final class OAuthRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function token(): array
    {
        return [
            'grant_type' => ['required', 'in:client_credentials,password,authorization_code'],
            'client_id' => ['nullable', 'string', 'max:120'],
            'client_secret' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'password' => ['nullable', 'string', 'min:8'],
            'scope' => ['nullable', 'string'],
        ];
    }
}
