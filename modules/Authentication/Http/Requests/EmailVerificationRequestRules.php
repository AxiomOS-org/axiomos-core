<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Requests;

final class EmailVerificationRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function verify(): array
    {
        return ['token' => ['required', 'string', 'min:16']];
    }
}
