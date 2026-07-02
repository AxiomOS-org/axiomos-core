<?php

declare(strict_types=1);

namespace Modules\Authentication\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Authentication\Domain\Models\AuthMfaMethod;

/**
 * @extends Factory<AuthMfaMethod>
 */
final class AuthMfaMethodFactory extends Factory
{
    protected $model = AuthMfaMethod::class;

    public function definition(): array
    {
        return [
            'method_type' => 'totp',
            'secret_encrypted' => base64_encode('ABCDEFGHIJKLMNOPQRSTUV234567'),
            'enabled' => true,
            'recovery_codes' => ['DEMO1234', 'DEMO5678'],
            'status' => 'active',
        ];
    }
}
