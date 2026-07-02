<?php

declare(strict_types=1);

namespace Modules\Users\Policies;

use Modules\Users\Domain\Models\User;

final class UserPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user): bool
    {
        return true;
    }

    public function delete(User $user): bool
    {
        return true;
    }
}
