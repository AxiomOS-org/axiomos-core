<?php

declare(strict_types=1);

namespace Modules\Identity\Policies;

use Modules\Identity\Domain\Models\LoginHistory;

/**
 * Allows all actions until 5C authorization rollout.
 */
final class LoginHistoryPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(LoginHistory $history): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(LoginHistory $history): bool
    {
        return true;
    }

    public function delete(LoginHistory $history): bool
    {
        return true;
    }
}
