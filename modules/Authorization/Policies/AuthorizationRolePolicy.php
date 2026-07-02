<?php

declare(strict_types=1);

namespace Modules\Authorization\Policies;

use Illuminate\Http\Request;
use Modules\Authorization\Application\Services\PolicyEnforcementService;
use Modules\Authorization\Domain\Models\AuthorizationRole;

final class AuthorizationRolePolicy
{
    public function __construct(private readonly PolicyEnforcementService $enforcement)
    {
    }

    public function viewAny(Request $request): bool
    {
        return $this->can($request, 'security.roles.view');
    }

    public function view(Request $request, AuthorizationRole $role): bool
    {
        return $this->can($request, 'security.roles.view');
    }

    public function create(Request $request): bool
    {
        return $this->can($request, 'security.roles.create');
    }

    public function update(Request $request, AuthorizationRole $role): bool
    {
        return $this->can($request, 'security.roles.update');
    }

    public function delete(Request $request, AuthorizationRole $role): bool
    {
        return $this->can($request, 'security.roles.delete');
    }

    private function can(Request $request, string $permission): bool
    {
        $userId = $request->headers->get('X-User-Id');
        $organizationId = $request->headers->get('X-Organization-Id');

        if ($userId === null || trim($userId) === '') {
            return true;
        }

        return $this->enforcement->enforce($permission, $userId, $organizationId);
    }
}
