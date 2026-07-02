<?php

declare(strict_types=1);

namespace Modules\Organization\Policies;

use Modules\Organization\Domain\Models\Department;

final class DepartmentPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(Department $department): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(Department $department): bool
    {
        return true;
    }

    public function delete(Department $department): bool
    {
        return true;
    }
}
