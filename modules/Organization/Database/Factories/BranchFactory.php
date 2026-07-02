<?php

declare(strict_types=1);

namespace Modules\Organization\Database\Factories;

use Modules\Organization\Domain\Models\Branch;
use Modules\Organization\Domain\Models\Company;

/**
 * @extends OrganizationEntityFactory<Branch>
 */
final class BranchFactory extends OrganizationEntityFactory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return array_merge($this->sharedAttributes(), [
            'company_id' => Company::factory(),
        ]);
    }
}
