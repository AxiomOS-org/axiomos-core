<?php

declare(strict_types=1);

namespace Modules\Identity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Identity\Domain\Models\Team;

/**
 * @extends Factory<Team>
 */
final class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        $suffix = bin2hex(random_bytes(3));

        return [
            'code' => 'TEAM-' . strtoupper($suffix),
            'name' => 'Team ' . $suffix,
            'description' => 'Factory team ' . $suffix,
            'status' => 'active',
        ];
    }
}
