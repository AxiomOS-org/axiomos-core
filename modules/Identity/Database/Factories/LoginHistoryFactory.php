<?php

declare(strict_types=1);

namespace Modules\Identity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Identity\Domain\Models\LoginHistory;

/**
 * @extends Factory<LoginHistory>
 */
final class LoginHistoryFactory extends Factory
{
    protected $model = LoginHistory::class;

    public function definition(): array
    {
        $suffix = bin2hex(random_bytes(3));

        return [
            'ip_address' => sprintf('10.20.%d.%d', random_int(1, 254), random_int(1, 254)),
            'user_agent' => 'AxiomOS Factory Login/' . $suffix,
            'success' => true,
            'logged_at' => now()->subMinutes(random_int(5, 120)),
            'status' => 'recorded',
        ];
    }
}
