<?php

declare(strict_types=1);

namespace Modules\Membership\Application\DTOs;

final readonly class CreateMembershipDTO
{
    /**
     * @param array<string, mixed>|null $scopes
     */
    public function __construct(
        public string $userId,
        public string $organizationId,
        public string $membershipType,
        public string $status = 'active',
        public ?array $scopes = null,
        public ?string $createdBy = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: (string) $data['user_id'],
            organizationId: (string) $data['organization_id'],
            membershipType: (string) $data['membership_type'],
            status: (string) ($data['status'] ?? 'active'),
            scopes: isset($data['scopes']) && is_array($data['scopes']) ? $data['scopes'] : null,
            createdBy: isset($data['created_by']) ? (string) $data['created_by'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(): array
    {
        return array_filter([
            'user_id' => $this->userId,
            'organization_id' => $this->organizationId,
            'membership_type' => $this->membershipType,
            'status' => $this->status,
            'scopes' => $this->scopes,
            'created_by' => $this->createdBy,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
