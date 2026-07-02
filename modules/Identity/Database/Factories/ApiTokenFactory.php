<?php

declare(strict_types=1);

namespace Modules\Identity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Identity\Domain\Models\ApiToken;

/**
 * @extends Factory<ApiToken>
 */
final class ApiTokenFactory extends Factory
{
    protected $model = ApiToken::class;

    public function definition(): array
    {
        $suffix = bin2hex(random_bytes(4));
        $plainToken = 'factory.' . $suffix . '.' . bin2hex(random_bytes(16));

        return [
            'name' => 'token-' . $suffix,
            'token_hash' => hash('sha256', $plainToken),
            'scopes' => ['identity:read', 'identity:write'],
            'expires_at' => now()->addDays(90),
            'last_used_at' => now()->subMinutes(random_int(1, 60)),
            'status' => 'active',
        ];
    }
}
