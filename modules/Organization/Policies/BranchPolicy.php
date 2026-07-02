<?php

declare(strict_types=1);

namespace Modules\Organization\Policies;

use Modules\Organization\Domain\Models\Branch;

final class BranchPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(Branch $branch): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(Branch $branch): bool
    {
        return true;
    }

    public function delete(Branch $branch): bool
    {
        return true;
    }
}
