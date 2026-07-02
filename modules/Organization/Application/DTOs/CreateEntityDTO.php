<?php

declare(strict_types=1);

namespace Modules\Organization\Application\DTOs;

use Modules\Organization\Domain\Enums\EntityStatus;

/**
 * Shared input shape for creating any organization hierarchy entity.
 */
final readonly class CreateEntityDTO
{
    /**
     * @param array<string, mixed>|null $settings
     */
    public function __construct(
        public string $code,
        public string $name,
        public ?string $description = null,
        public ?string $slug = null,
        public ?string $logo = null,
        public EntityStatus $status = EntityStatus::Active,
        public string $timezone = 'UTC',
        public string $currency = 'USD',
        public string $locale = 'en',
        public string $country = 'US',
        public ?array $settings = null,
        public ?string $createdBy = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: (string) $data['code'],
            name: (string) $data['name'],
            description: isset($data['description']) ? (string) $data['description'] : null,
            slug: isset($data['slug']) ? (string) $data['slug'] : null,
            logo: isset($data['logo']) ? (string) $data['logo'] : null,
            status: isset($data['status'])
                ? EntityStatus::from((string) $data['status'])
                : EntityStatus::Active,
            timezone: (string) ($data['timezone'] ?? 'UTC'),
            currency: (string) ($data['currency'] ?? 'USD'),
            locale: (string) ($data['locale'] ?? 'en'),
            country: (string) ($data['country'] ?? 'US'),
            settings: isset($data['settings']) && is_array($data['settings']) ? $data['settings'] : null,
            createdBy: isset($data['created_by']) ? (string) $data['created_by'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(): array
    {
        return array_filter([
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'logo' => $this->logo,
            'status' => $this->status->value,
            'timezone' => $this->timezone,
            'currency' => $this->currency,
            'locale' => $this->locale,
            'country' => $this->country,
            'settings' => $this->settings,
            'created_by' => $this->createdBy,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
