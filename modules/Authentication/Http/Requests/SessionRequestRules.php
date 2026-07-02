<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Requests;

final class SessionRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function list(): array
    {
        return ['user_id' => ['required', 'uuid']];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function revoke(): array
    {
        return [
            'session_id' => ['required', 'uuid'],
        ];
    }
}
