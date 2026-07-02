<?php

declare(strict_types=1);

namespace Modules\Users\Application\DTOs;

final readonly class CreateUserDTO
{
    /**
     * @param array<string, mixed>|null $settings
     */
    public function __construct(
        public string $identityId,
        public string $username,
        public string $email,
        public string $displayName,
        public string $status = 'active',
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
            identityId: (string) $data['identity_id'],
            username: (string) $data['username'],
            email: (string) $data['email'],
            displayName: (string) $data['display_name'],
            status: (string) ($data['status'] ?? 'active'),
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
            'identity_id' => $this->identityId,
            'username' => $this->username,
            'email' => $this->email,
            'display_name' => $this->displayName,
            'status' => $this->status,
            'settings' => $this->settings,
            'created_by' => $this->createdBy,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
