<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\DTOs;

final readonly class PasswordChangeDTO
{
    public function __construct(
        public string $userId,
        public string $currentPassword,
        public string $newPassword,
        public ?string $organizationId = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: (string) $data['user_id'],
            currentPassword: (string) $data['current_password'],
            newPassword: (string) $data['new_password'],
            organizationId: isset($data['organization_id']) ? (string) $data['organization_id'] : null,
        );
    }
}
