<?php

declare(strict_types=1);

namespace Modules\Authentication\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Authentication\Domain\Models\AuthCredential;

/**
 * @extends Factory<AuthCredential>
 */
final class AuthCredentialFactory extends Factory
{
    protected $model = AuthCredential::class;

    public function definition(): array
    {
        return [
            'password_hash' => password_hash('AxiomOS@2026!', PASSWORD_ARGON2ID),
            'failed_attempts' => 0,
            'must_change_password' => false,
            'status' => 'active',
        ];
    }
}
