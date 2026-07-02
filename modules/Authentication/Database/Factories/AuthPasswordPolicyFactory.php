<?php

declare(strict_types=1);

namespace Modules\Authentication\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Authentication\Domain\Models\AuthPasswordPolicy;

/**
 * @extends Factory<AuthPasswordPolicy>
 */
final class AuthPasswordPolicyFactory extends Factory
{
    protected $model = AuthPasswordPolicy::class;

    public function definition(): array
    {
        return [
            'rules' => ['require_uppercase' => true, 'require_lowercase' => true, 'require_numeric' => true, 'require_symbol' => true],
            'min_length' => 12,
            'expiry_days' => 90,
            'history_count' => 5,
            'lockout_threshold' => 5,
            'lockout_minutes' => 15,
            'status' => 'active',
        ];
    }
}
