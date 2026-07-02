<?php

declare(strict_types=1);

namespace Modules\Identity\Policies;

use Modules\Identity\Domain\Models\ApiToken;

/**
 * Allows all actions until 5C authorization rollout.
 */
final class ApiTokenPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(ApiToken $token): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(ApiToken $token): bool
    {
        return true;
    }

    public function delete(ApiToken $token): bool
    {
        return true;
    }
}
