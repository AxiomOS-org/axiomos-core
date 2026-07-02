<?php

declare(strict_types=1);

namespace Modules\Users\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Users\Domain\Models\User;

/**
 * @extends Factory<User>
 */
final class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $suffix = bin2hex(random_bytes(3));

        return [
            'username' => 'user.' . $suffix,
            'email' => 'user.' . $suffix . '@axiomos.local',
            'display_name' => 'User ' . $suffix,
            'status' => 'active',
            'settings' => ['theme' => 'default'],
        ];
    }
}
