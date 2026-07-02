<?php

declare(strict_types=1);

namespace Modules\Identity\Policies;

use Modules\Identity\Domain\Models\Team;

/**
 * Allows all actions until 5C authorization rollout.
 */
final class TeamPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(Team $team): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(Team $team): bool
    {
        return true;
    }

    public function delete(Team $team): bool
    {
        return true;
    }
}
