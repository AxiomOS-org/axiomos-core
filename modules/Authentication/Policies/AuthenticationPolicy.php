<?php

declare(strict_types=1);

namespace Modules\Authentication\Policies;

final class AuthenticationPolicy
{
    public function access(): bool
    {
        return true;
    }
}
