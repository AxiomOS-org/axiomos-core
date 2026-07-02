<?php

declare(strict_types=1);

namespace Modules\Identity\Policies;

use Modules\Identity\Domain\Models\EmployeeProfile;

/**
 * Allows all actions until 5C authorization rollout.
 */
final class EmployeeProfilePolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(EmployeeProfile $profile): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(EmployeeProfile $profile): bool
    {
        return true;
    }

    public function delete(EmployeeProfile $profile): bool
    {
        return true;
    }
}
