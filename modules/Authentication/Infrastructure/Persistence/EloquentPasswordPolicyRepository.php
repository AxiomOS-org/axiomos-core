<?php

declare(strict_types=1);

namespace Modules\Authentication\Infrastructure\Persistence;

use Modules\Authentication\Domain\Models\AuthPasswordPolicy;
use Modules\Authentication\Domain\Repositories\Contracts\PasswordPolicyRepositoryInterface;

final class EloquentPasswordPolicyRepository implements PasswordPolicyRepositoryInterface
{
    public function findByOrganization(?string $organizationId): ?AuthPasswordPolicy
    {
        if ($organizationId !== null) {
            $policy = AuthPasswordPolicy::query()
                ->where('organization_id', $organizationId)
                ->orderByDesc('created_at')
                ->first();

            if ($policy !== null) {
                return $policy;
            }
        }

        return AuthPasswordPolicy::query()
            ->whereNull('organization_id')
            ->orderByDesc('created_at')
            ->first();
    }

    public function create(array $attributes): AuthPasswordPolicy
    {
        return AuthPasswordPolicy::query()->create($attributes);
    }
}
