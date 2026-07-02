<?php

declare(strict_types=1);

namespace Modules\Organization\Policies;

use Modules\Organization\Domain\Models\Company;

final class CompanyPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(Company $company): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(Company $company): bool
    {
        return true;
    }

    public function delete(Company $company): bool
    {
        return true;
    }
}
