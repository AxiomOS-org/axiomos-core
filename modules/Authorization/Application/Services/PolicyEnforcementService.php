<?php

declare(strict_types=1);

namespace Modules\Authorization\Application\Services;

final class PolicyEnforcementService
{
    public function __construct(private readonly AuthorizationService $authorization)
    {
    }

    public function enforce(string $permission, ?string $userId, ?string $organizationId = null): bool
    {
        if ($userId === null || trim($userId) === '') {
            return false;
        }

        return $this->authorization->userHasPermission($userId, $permission, $organizationId);
    }
}
