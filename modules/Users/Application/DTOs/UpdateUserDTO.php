<?php

declare(strict_types=1);

namespace Modules\Users\Application\DTOs;

final readonly class UpdateUserDTO
{
    /**
     * @param array<string, mixed>|null $settings
     */
    public function __construct(
        public ?string $identityId = null,
        public ?string $username = null,
        public ?string $email = null,
        public ?string $displayName = null,
        public ?string $status = null,
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
            identityId: isset($data['identity_id']) ? (string) $data['identity_id'] : null,
            username: isset($data['username']) ? (string) $data['username'] : null,
            email: isset($data['email']) ? (string) $data['email'] : null,
            displayName: isset($data['display_name']) ? (string) $data['display_name'] : null,
            status: isset($data['status']) ? (string) $data['status'] : null,
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
            'identity_id' => $this->identityId,
            'username' => $this->username,
            'email' => $this->email,
            'display_name' => $this->displayName,
            'status' => $this->status,
            'settings' => $this->settings,
            'updated_by' => $this->updatedBy,
        ] as $key => $value) {
            if ($value !== null) {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }
}
