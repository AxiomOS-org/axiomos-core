<?php

declare(strict_types=1);

namespace Modules\Organization\Database\Factories;

use Modules\Organization\Domain\Models\Branch;
use Modules\Organization\Domain\Models\Department;

/**
 * @extends OrganizationEntityFactory<Department>
 */
final class DepartmentFactory extends OrganizationEntityFactory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return array_merge($this->sharedAttributes(), [
            'branch_id' => Branch::factory(),
            'parent_id' => null,
        ]);
    }
}
