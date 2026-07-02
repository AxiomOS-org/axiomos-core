<?php

declare(strict_types=1);

namespace Modules\Organization\Database\Seeders;

use Modules\Organization\Domain\Enums\EntityStatus;
use Modules\Organization\Domain\Models\Branch;
use Modules\Organization\Domain\Models\Company;
use Modules\Organization\Domain\Models\Department;
use Modules\Organization\Domain\Models\Organization;

/**
 * Seeds a demo hierarchy: 1 organization -> 1 company -> 2 branches -> departments.
 */
final class OrganizationSeeder
{
    public function run(): void
    {
        $organization = Organization::query()->create([
            'code' => 'AXIOM',
            'name' => 'AxiomOS Demo Organization',
            'slug' => 'axiomos-demo',
            'description' => 'Demo tenant for local development',
            'status' => EntityStatus::Active->value,
            'timezone' => 'Asia/Karachi',
            'currency' => 'PKR',
            'locale' => 'en',
            'country' => 'PK',
            'settings' => ['theme' => 'default'],
        ]);

        $company = Company::query()->create([
            'organization_id' => $organization->id,
            'code' => 'HQ',
            'name' => 'AxiomOS Headquarters',
            'slug' => 'hq',
            'description' => 'Head office',
            'status' => EntityStatus::Active->value,
            'timezone' => 'Asia/Karachi',
            'currency' => 'PKR',
            'locale' => 'en',
            'country' => 'PK',
            'settings' => ['theme' => 'default'],
        ]);

        foreach (['lahore', 'karachi'] as $city) {
            $branch = Branch::query()->create([
                'company_id' => $company->id,
                'code' => strtoupper($city),
                'name' => ucfirst($city) . ' Branch',
                'slug' => $city,
                'description' => ucfirst($city) . ' regional office',
                'status' => EntityStatus::Active->value,
                'timezone' => 'Asia/Karachi',
                'currency' => 'PKR',
                'locale' => 'en',
                'country' => 'PK',
                'settings' => ['theme' => 'default'],
            ]);

            foreach (['operations' => 'Operations', 'finance' => 'Finance'] as $slug => $name) {
                Department::query()->create([
                    'branch_id' => $branch->id,
                    'code' => strtoupper(substr($slug, 0, 3)),
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $name . ' department',
                    'status' => EntityStatus::Active->value,
                    'timezone' => 'Asia/Karachi',
                    'currency' => 'PKR',
                    'locale' => 'en',
                    'country' => 'PK',
                    'settings' => ['theme' => 'default'],
                ]);
            }
        }
    }
}
