<?php

declare(strict_types=1);

namespace Modules\Organization\Policies;

use Modules\Organization\Domain\Models\Organization;

/**
 * Authorization policy for organizations.
 *
 * Allows all actions until the Authentication/Authorization modules enforce
 * real RBAC in Sprint 5+.
 */
final class OrganizationPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(Organization $organization): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(Organization $organization): bool
    {
        return true;
    }

    public function delete(Organization $organization): bool
    {
        return true;
    }
}
