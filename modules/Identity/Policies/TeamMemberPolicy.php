<?php

declare(strict_types=1);

namespace Modules\Identity\Policies;

use Modules\Identity\Domain\Models\TeamMember;

/**
 * Allows all actions until 5C authorization rollout.
 */
final class TeamMemberPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(TeamMember $member): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(TeamMember $member): bool
    {
        return true;
    }

    public function delete(TeamMember $member): bool
    {
        return true;
    }
}