<?php

declare(strict_types=1);

namespace Modules\Organization\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Organization\Domain\Enums\EntityStatus;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends Factory<TModel>
 */
abstract class OrganizationEntityFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    protected function sharedAttributes(): array
    {
        $suffix = bin2hex(random_bytes(3));
        $name = 'Entity ' . $suffix;

        return [
            'code' => strtoupper(substr($suffix, 0, 6)),
            'name' => $name,
            'description' => 'Generated test entity',
            'slug' => 'entity-' . $suffix,
            'logo' => null,
            'status' => EntityStatus::Active->value,
            'timezone' => 'UTC',
            'currency' => 'USD',
            'locale' => 'en',
            'country' => 'US',
            'settings' => ['theme' => 'default'],
        ];
    }
}
