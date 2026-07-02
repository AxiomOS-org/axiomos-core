<?php

declare(strict_types=1);

namespace Modules\Identity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Identity\Domain\Models\TeamMember;

/**
 * @extends Factory<TeamMember>
 */
final class TeamMemberFactory extends Factory
{
    protected $model = TeamMember::class;

    public function definition(): array
    {
        $roles = ['member', 'lead', 'admin'];

        return [
            'role' => $roles[random_int(0, count($roles) - 1)],
        ];
    }
}
