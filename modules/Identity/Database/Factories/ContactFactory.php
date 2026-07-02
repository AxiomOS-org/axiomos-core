<?php

declare(strict_types=1);

namespace Modules\Identity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Identity\Domain\Models\Contact;

/**
 * @extends Factory<Contact>
 */
final class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        $suffix = bin2hex(random_bytes(3));
        $types = ['email', 'phone', 'mobile'];
        $contactType = $types[random_int(0, count($types) - 1)];

        $value = match ($contactType) {
            'email' => 'contact.' . $suffix . '@axiomos.local',
            default => '+1202555' . random_int(1000, 9999),
        };

        return [
            'contact_type' => $contactType,
            'value' => $value,
            'is_primary' => random_int(0, 1) === 1,
            'status' => 'active',
        ];
    }
}
