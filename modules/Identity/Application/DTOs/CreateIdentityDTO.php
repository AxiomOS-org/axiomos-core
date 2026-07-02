<?php

declare(strict_types=1);

namespace Modules\Identity\Application\DTOs;

final readonly class CreateIdentityDTO
{
    /**
     * @param array<string, mixed>|null $metadata
     */
    public function __construct(
        public string $identityType,
        public string $code,
        public string $displayName,
        public ?string $organizationId = null,
        public ?string $legalName = null,
        public ?string $email = null,
        public ?string $phone = null,
        public string $status = 'active',
        public ?array $metadata = null,
        public ?string $createdBy = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            identityType: (string) $data['identity_type'],
            code: (string) $data['code'],
            displayName: (string) $data['display_name'],
            organizationId: isset($data['organization_id']) ? (string) $data['organization_id'] : null,
            legalName: isset($data['legal_name']) ? (string) $data['legal_name'] : null,
            email: isset($data['email']) ? (string) $data['email'] : null,
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
            status: (string) ($data['status'] ?? 'active'),
            metadata: isset($data['metadata']) && is_array($data['metadata']) ? $data['metadata'] : null,
            createdBy: isset($data['created_by']) ? (string) $data['created_by'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(): array
    {
        return array_filter([
            'identity_type' => $this->identityType,
            'code' => $this->code,
            'display_name' => $this->displayName,
            'organization_id' => $this->organizationId,
            'legal_name' => $this->legalName,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'created_by' => $this->createdBy,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
