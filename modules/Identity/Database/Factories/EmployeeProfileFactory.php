<?php

declare(strict_types=1);

namespace Modules\Identity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Identity\Domain\Models\EmployeeProfile;

/**
 * @extends Factory<EmployeeProfile>
 */
final class EmployeeProfileFactory extends Factory
{
    protected $model = EmployeeProfile::class;

    public function definition(): array
    {
        $suffix = bin2hex(random_bytes(3));

        return [
            'employee_number' => 'EMP-' . strtoupper($suffix),
            'job_title' => 'Operations Specialist',
            'hire_date' => now()->subDays(random_int(30, 365))->toDateString(),
            'status' => 'active',
            'metadata' => ['factory' => true],
        ];
    }
}
