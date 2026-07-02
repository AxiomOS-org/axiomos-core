<?php

declare(strict_types=1);

namespace Modules\Organization\Application\DTOs;

use Modules\Organization\Domain\Enums\EntityStatus;

/**
 * Shared input shape for updating any organization hierarchy entity.
 */
final readonly class UpdateEntityDTO
{
    /**
     * @param array<string, mixed>|null $settings
     */
    public function __construct(
        public ?string $code = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?string $slug = null,
        public ?string $logo = null,
        public ?EntityStatus $status = null,
        public ?string $timezone = null,
        public ?string $currency = null,
        public ?string $locale = null,
        public ?string $country = null,
        public ?array $settings = null,
        public ?string $updatedBy = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: isset($data['code']) ? (string) $data['code'] : null,
            name: isset($data['name']) ? (string) $data['name'] : null,
            description: array_key_exists('description', $data) ? (string) $data['description'] : null,
            slug: isset($data['slug']) ? (string) $data['slug'] : null,
            logo: array_key_exists('logo', $data) ? (string) $data['logo'] : null,
            status: isset($data['status']) ? EntityStatus::from((string) $data['status']) : null,
            timezone: isset($data['timezone']) ? (string) $data['timezone'] : null,
            currency: isset($data['currency']) ? (string) $data['currency'] : null,
            locale: isset($data['locale']) ? (string) $data['locale'] : null,
            country: isset($data['country']) ? (string) $data['country'] : null,
            settings: isset($data['settings']) && is_array($data['settings']) ? $data['settings'] : null,
            updatedBy: isset($data['updated_by']) ? (string) $data['updated_by'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(): array
    {
        $attributes = [];

        foreach ([
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'logo' => $this->logo,
            'timezone' => $this->timezone,
            'currency' => $this->currency,
            'locale' => $this->locale,
            'country' => $this->country,
            'settings' => $this->settings,
            'updated_by' => $this->updatedBy,
        ] as $key => $value) {
            if ($value !== null) {
                $attributes[$key] = $value;
            }
        }

        if ($this->status !== null) {
            $attributes['status'] = $this->status->value;
        }

        return $attributes;
    }
}
