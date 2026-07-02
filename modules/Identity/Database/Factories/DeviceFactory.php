<?php

declare(strict_types=1);

namespace Modules\Identity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Identity\Domain\Models\Device;

/**
 * @extends Factory<Device>
 */
final class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        $suffix = bin2hex(random_bytes(4));
        $types = ['workstation', 'mobile', 'tablet'];

        return [
            'device_type' => $types[random_int(0, count($types) - 1)],
            'fingerprint' => hash('sha256', 'device-' . $suffix),
            'user_agent' => 'AxiomOS Factory Agent/' . $suffix,
            'last_seen_at' => now()->subHours(random_int(1, 48)),
            'status' => 'active',
        ];
    }
}
