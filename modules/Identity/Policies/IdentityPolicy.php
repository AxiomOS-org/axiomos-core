<?php

declare(strict_types=1);

namespace Modules\Identity\Policies;

use Modules\Identity\Domain\Models\Identity;

/**
 * Allows all actions until 5C authorization rollout.
 */
final class IdentityPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(Identity $identity): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(Identity $identity): bool
    {
        return true;
    }

    public function delete(Identity $identity): bool
    {
        return true;
    }
}
