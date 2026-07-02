<?php

declare(strict_types=1);

namespace Modules\Identity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Identity\Domain\Models\IdentitySession;

/**
 * @extends Factory<IdentitySession>
 */
final class IdentitySessionFactory extends Factory
{
    protected $model = IdentitySession::class;

    public function definition(): array
    {
        $suffix = bin2hex(random_bytes(4));

        return [
            'session_token_hash' => hash('sha256', 'session-' . $suffix),
            'ip_address' => sprintf('10.20.%d.%d', random_int(1, 254), random_int(1, 254)),
            'user_agent' => 'AxiomOS Factory Session/' . $suffix,
            'started_at' => now()->subHours(random_int(1, 12)),
            'expires_at' => now()->addHours(24),
            'status' => 'active',
        ];
    }
}
