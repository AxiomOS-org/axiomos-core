<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Requests;

final class LoginRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function login(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
            'ip_address' => ['nullable', 'string', 'max:64'],
            'user_agent' => ['nullable', 'string', 'max:1000'],
            'remember_device' => ['nullable', 'boolean'],
            'device_fingerprint' => ['nullable', 'string', 'max:255'],
        ];
    }
}
