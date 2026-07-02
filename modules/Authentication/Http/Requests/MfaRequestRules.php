<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Requests;

final class MfaRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function enable(): array
    {
        return [
            'user_id' => ['required', 'uuid'],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function verify(): array
    {
        return [
            'user_id' => ['required', 'uuid'],
            'code' => ['required', 'string', 'size:6'],
        ];
    }
}
