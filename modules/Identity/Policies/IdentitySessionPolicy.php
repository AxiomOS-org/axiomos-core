<?php

declare(strict_types=1);

namespace Modules\Identity\Policies;

use Modules\Identity\Domain\Models\IdentitySession;

/**
 * Allows all actions until 5C authorization rollout.
 */
final class IdentitySessionPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(IdentitySession $session): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(IdentitySession $session): bool
    {
        return true;
    }

    public function delete(IdentitySession $session): bool
    {
        return true;
    }
}
