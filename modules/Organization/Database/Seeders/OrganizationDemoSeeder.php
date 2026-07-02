<?php

declare(strict_types=1);

namespace Modules\Organization\Database\Seeders;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use Modules\Organization\Domain\Enums\EntityStatus;
use Modules\Organization\Domain\Models\Branch;
use Modules\Organization\Domain\Models\Company;
use Modules\Organization\Domain\Models\Department;
use Modules\Organization\Domain\Models\Organization;

/**
 * Seeds enterprise demo data: 10 organizations, 50 companies, 200 branches, 500 departments.
 */
final class OrganizationDemoSeeder
{
    private Generator $faker;

    /** @var list<string> */
    private array $departmentNames = [
        'Operations', 'Finance', 'Human Resources', 'Sales', 'IT', 'Logistics', 'Procurement',
        'Marketing', 'Legal', 'Customer Success',
    ];

    public function __construct()
    {
        $this->faker = FakerFactory::create();
    }

    public function run(): void
    {
        if (Organization::query()->count() > 0) {
            return;
        }

        $organizations = [];

        for ($i = 0; $i < 10; $i++) {
            $name = $i === 0
                ? 'AxiomOS Demo Organization'
                : $this->faker->company() . ' Group';

            $code = $i === 0 ? 'AXIOM' : strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name) ?: 'ORG', 0, 6)) . $i;

            $organizations[] = Organization::query()->create([
                'code' => $code,
                'name' => $name,
                'slug' => $i === 0 ? 'axiomos-demo' : $this->faker->slug(2) . '-' . $i,
                'description' => $this->faker->catchPhrase(),
                'status' => EntityStatus::Active->value,
                'timezone' => $this->faker->timezone(),
                'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP', 'PKR', 'AED']),
                'locale' => 'en',
                'country' => $this->faker->countryCode(),
                'settings' => ['theme' => 'default', 'industry' => $this->faker->word()],
            ]);
        }

        $companyCount = 0;
        $branchCount = 0;
        $departmentCount = 0;

        foreach ($organizations as $orgIndex => $organization) {
            for ($c = 0; $c < 5; $c++) {
                $companyCount++;
                $companyName = $this->faker->company();

                $company = Company::query()->create([
                    'organization_id' => $organization->id,
                    'code' => 'CO' . str_pad((string) $companyCount, 3, '0', STR_PAD_LEFT),
                    'name' => $companyName,
                    'slug' => $this->faker->slug(2) . '-co' . $companyCount,
                    'description' => $this->faker->sentence(),
                    'status' => EntityStatus::Active->value,
                    'timezone' => $organization->timezone,
                    'currency' => $organization->currency,
                    'locale' => 'en',
                    'country' => $organization->country,
                    'settings' => ['theme' => 'default'],
                ]);

                for ($b = 0; $b < 4; $b++) {
                    $branchCount++;
                    $city = $this->faker->city();

                    $branch = Branch::query()->create([
                        'company_id' => $company->id,
                        'code' => 'BR' . str_pad((string) $branchCount, 3, '0', STR_PAD_LEFT),
                        'name' => $city . ' Branch',
                        'slug' => $this->faker->slug(1) . '-br' . $branchCount,
                        'description' => $city . ' regional office',
                        'status' => EntityStatus::Active->value,
                        'timezone' => $organization->timezone,
                        'currency' => $organization->currency,
                        'locale' => 'en',
                        'country' => $organization->country,
                        'settings' => ['theme' => 'default'],
                    ]);

                    $departmentsForBranch = $branchCount <= 100 ? 3 : 2;

                    for ($d = 0; $d < $departmentsForBranch; $d++) {
                        $departmentCount++;
                        $deptName = $this->departmentNames[($departmentCount + $d) % count($this->departmentNames)];

                        Department::query()->create([
                            'branch_id' => $branch->id,
                            'code' => 'DP' . str_pad((string) $departmentCount, 3, '0', STR_PAD_LEFT),
                            'name' => $deptName,
                            'slug' => strtolower(str_replace(' ', '-', $deptName)) . '-dp' . $departmentCount,
                            'description' => $deptName . ' department',
                            'status' => EntityStatus::Active->value,
                            'timezone' => $organization->timezone,
                            'currency' => $organization->currency,
                            'locale' => 'en',
                            'country' => $organization->country,
                            'settings' => ['theme' => 'default'],
                        ]);
                    }
                }
            }
        }
    }
}
