<?php

declare(strict_types=1);

namespace Modules\Organization\Database\Factories;

use Modules\Organization\Domain\Models\Company;
use Modules\Organization\Domain\Models\Organization;

/**
 * @extends OrganizationEntityFactory<Company>
 */
final class CompanyFactory extends OrganizationEntityFactory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return array_merge($this->sharedAttributes(), [
            'organization_id' => Organization::factory(),
        ]);
    }
}
