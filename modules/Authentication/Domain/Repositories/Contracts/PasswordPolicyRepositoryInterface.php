<?php

declare(strict_types=1);

namespace Modules\Authentication\Domain\Repositories\Contracts;

use Modules\Authentication\Domain\Models\AuthPasswordPolicy;

interface PasswordPolicyRepositoryInterface
{
    public function findByOrganization(?string $organizationId): ?AuthPasswordPolicy;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): AuthPasswordPolicy;
}
