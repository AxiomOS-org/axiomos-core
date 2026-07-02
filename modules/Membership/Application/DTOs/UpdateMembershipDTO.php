<?php

declare(strict_types=1);

namespace Modules\Membership\Application\DTOs;

final readonly class UpdateMembershipDTO
{
    /**
     * @param array<string, mixed>|null $scopes
     */
    public function __construct(
        public ?string $userId = null,
        public ?string $organizationId = null,
        public ?string $membershipType = null,
        public ?string $status = null,
        public ?array $scopes = null,
        public ?string $updatedBy = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: isset($data['user_id']) ? (string) $data['user_id'] : null,
            organizationId: isset($data['organization_id']) ? (string) $data['organization_id'] : null,
            membershipType: isset($data['membership_type']) ? (string) $data['membership_type'] : null,
            status: isset($data['status']) ? (string) $data['status'] : null,
            scopes: isset($data['scopes']) && is_array($data['scopes']) ? $data['scopes'] : null,
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
            'user_id' => $this->userId,
            'organization_id' => $this->organizationId,
            'membership_type' => $this->membershipType,
            'status' => $this->status,
            'scopes' => $this->scopes,
            'updated_by' => $this->updatedBy,
        ] as $key => $value) {
            if ($value !== null) {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }
}
