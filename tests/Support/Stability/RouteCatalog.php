<?php

declare(strict_types=1);

namespace Tests\Support\Stability;

/**
 * Canonical browser GET pages for stability validation.
 *
 * @return list<string>
 */
final class RouteCatalog
{
    public static function browserGetPages(): array
    {
        return [
            '/',
            '/health',
            '/metrics',
            '/login',
            '/forgot-password',
            '/reset-password',
            '/email-verification',
            '/organizations',
            '/companies',
            '/branches',
            '/departments',
            '/users',
            '/memberships',
            '/identity',
            '/identity/dashboard',
            '/identity/identities',
            '/identity/teams',
            '/identity/team-members',
            '/identity/employee-profiles',
            '/identity/contacts',
            '/identity/devices',
            '/identity/identity-sessions',
            '/identity/login-history',
            '/identity/api-tokens',
            '/security',
            '/security/dashboard',
            '/security/roles',
            '/security/permissions',
            '/security/sessions',
            '/security/login-history',
        ];
    }

    /**
     * @return list<class-string>
     */
    public static function resolvableSingletons(): array
    {
        return [
            \Modules\Authentication\Application\Services\AuthenticationService::class,
            \Modules\Authentication\Application\Services\PasswordService::class,
            \Modules\Authentication\Application\Services\SessionManagerService::class,
            \Modules\Authentication\Http\Controllers\Api\AuthenticationApiController::class,
            \Modules\Authorization\Application\Services\AuthorizationService::class,
            \Modules\Authorization\Application\Services\RoleService::class,
            \Modules\Authorization\Http\Controllers\Api\RoleApiController::class,
            \Modules\Users\Application\Services\UserService::class,
            \Modules\Membership\Application\Services\MembershipService::class,
            \Modules\Organization\Application\Services\OrganizationService::class,
            \Modules\Identity\Application\Services\IdentityService::class,
        ];
    }
}
