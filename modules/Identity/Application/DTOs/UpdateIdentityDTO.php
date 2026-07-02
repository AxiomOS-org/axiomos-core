<?php

declare(strict_types=1);

namespace Modules\Identity\Application\DTOs;

final readonly class UpdateIdentityDTO
{
    /**
     * @param array<string, mixed>|null $metadata
     */
    public function __construct(
        public ?string $identityType = null,
        public ?string $code = null,
        public ?string $displayName = null,
        public ?string $organizationId = null,
        public ?string $legalName = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $status = null,
        public ?array $metadata = null,
        public ?string $updatedBy = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            identityType: isset($data['identity_type']) ? (string) $data['identity_type'] : null,
            code: isset($data['code']) ? (string) $data['code'] : null,
            displayName: isset($data['display_name']) ? (string) $data['display_name'] : null,
            organizationId: array_key_exists('organization_id', $data) ? (string) $data['organization_id'] : null,
            legalName: array_key_exists('legal_name', $data) ? (string) $data['legal_name'] : null,
            email: array_key_exists('email', $data) ? (string) $data['email'] : null,
            phone: array_key_exists('phone', $data) ? (string) $data['phone'] : null,
            status: isset($data['status']) ? (string) $data['status'] : null,
            metadata: isset($data['metadata']) && is_array($data['metadata']) ? $data['metadata'] : null,
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
            'identity_type' => $this->identityType,
            'code' => $this->code,
            'display_name' => $this->displayName,
            'organization_id' => $this->organizationId,
            'legal_name' => $this->legalName,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'updated_by' => $this->updatedBy,
        ] as $key => $value) {
            if ($value !== null) {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }
}
