<?php

declare(strict_types=1);

namespace Modules\Membership\Policies;

use Modules\Membership\Domain\Models\Membership;

final class MembershipPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(Membership $membership): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(Membership $membership): bool
    {
        return true;
    }

    public function delete(Membership $membership): bool
    {
        return true;
    }
}
