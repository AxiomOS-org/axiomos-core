<?php

declare(strict_types=1);

namespace Modules\Membership\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Membership\Domain\Models\Membership;

/**
 * @extends Factory<Membership>
 */
final class MembershipFactory extends Factory
{
    protected $model = Membership::class;

    public function definition(): array
    {
        return [
            'membership_type' => 'member',
            'status' => 'active',
            'scopes' => ['default' => true],
        ];
    }
}
