<?php

declare(strict_types=1);

namespace Modules\Identity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Identity\Domain\Models\Identity;

/**
 * @extends Factory<Identity>
 */
final class IdentityFactory extends Factory
{
    protected $model = Identity::class;

    public function definition(): array
    {
        $suffix = bin2hex(random_bytes(3));

        return [
            'identity_type' => 'employee',
            'code' => 'ID-' . strtoupper($suffix),
            'display_name' => 'Identity ' . $suffix,
            'legal_name' => 'Identity Legal ' . $suffix,
            'email' => 'identity.' . $suffix . '@axiomos.local',
            'phone' => '+1202555' . random_int(1000, 9999),
            'status' => 'active',
            'metadata' => ['factory' => true],
        ];
    }
}
