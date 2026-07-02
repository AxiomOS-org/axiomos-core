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
            '/accounting',
            '/accounting/dashboard',
            '/accounting/accounts',
            '/accounting/documents',
            '/accounting/journals',
            '/accounting/fiscal-years',
            '/accounting/periods',
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
            \Modules\Accounting\Application\Services\PostingEngine::class,
            \Modules\Accounting\Application\Services\ReversalEngine::class,
            \Modules\Accounting\Application\Services\JournalEngine::class,
            \Modules\Accounting\Application\Services\LedgerEngine::class,
            \Modules\Accounting\Application\Services\DocumentEngine::class,
            \Modules\Accounting\Application\Services\VoucherEngine::class,
            \Modules\Accounting\Application\Services\ChartOfAccountsService::class,
            \Modules\Accounting\Application\Services\FiscalYearService::class,
            \Modules\Accounting\Application\Services\AccountingPeriodService::class,
            \Modules\Accounting\Application\Services\TrialBalanceService::class,
            \Modules\Accounting\Application\Services\BalanceSheetService::class,
            \Modules\Accounting\Application\Services\ProfitAndLossService::class,
            \Modules\Accounting\Application\Services\CashFlowService::class,
            \Modules\Accounting\Application\Services\MultiCurrencyService::class,
            \Modules\Accounting\Application\Services\CostCenterService::class,
            \Modules\Accounting\Application\Services\DocumentService::class,
            \Modules\Accounting\Application\Services\AccountingPlatformHooks::class,
            \Modules\Accounting\Domain\Services\Contracts\PostingEngineInterface::class,
            \Modules\Accounting\Http\Controllers\Api\AccountingApiController::class,
            \Modules\Accounting\Http\Controllers\Web\AccountingDashboardWebController::class,
            \Modules\Accounting\Http\Controllers\Web\AccountingCrudWebController::class,
        ];
    }
}
