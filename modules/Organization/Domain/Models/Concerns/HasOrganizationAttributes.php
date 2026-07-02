<?php

declare(strict_types=1);

namespace Modules\Organization\Domain\Models\Concerns;

/**
 * Shared columns for Organization, Company, Branch and Department.
 */
trait HasOrganizationAttributes
{
    /**
     * @return list<string>
     */
    protected function organizationAttributeFillable(): array
    {
        return [
            'code',
            'name',
            'description',
            'slug',
            'logo',
            'status',
            'timezone',
            'currency',
            'locale',
            'country',
            'settings',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function organizationAttributeCasts(): array
    {
        return [
            'settings' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
