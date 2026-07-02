<?php

declare(strict_types=1);

namespace Modules\Organization\Database\Factories;

use Modules\Organization\Domain\Models\Organization;

/**
 * @extends OrganizationEntityFactory<Organization>
 */
final class OrganizationFactory extends OrganizationEntityFactory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        return $this->sharedAttributes();
    }
}
