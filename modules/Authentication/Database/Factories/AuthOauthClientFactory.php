<?php

declare(strict_types=1);

namespace Modules\Authentication\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Authentication\Domain\Models\AuthOauthClient;

/**
 * @extends Factory<AuthOauthClient>
 */
final class AuthOauthClientFactory extends Factory
{
    protected $model = AuthOauthClient::class;

    public function definition(): array
    {
        $secret = 'demo-client-secret';

        return [
            'client_id' => 'client-' . bin2hex(random_bytes(4)),
            'client_secret_hash' => password_hash($secret, PASSWORD_ARGON2ID),
            'name' => 'Demo OAuth Client',
            'redirect_uris' => ['https://localhost/callback'],
            'scopes' => ['auth:read'],
            'status' => 'active',
        ];
    }
}
