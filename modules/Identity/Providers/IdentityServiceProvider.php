<?php

declare(strict_types=1);

namespace Modules\Identity\Providers;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\Identity\Application\Services\ApiTokenService;
use Modules\Identity\Application\Services\ContactService;
use Modules\Identity\Application\Services\DeviceService;
use Modules\Identity\Application\Services\EmployeeProfileService;
use Modules\Identity\Application\Services\IdentityPlatformHooks;
use Modules\Identity\Application\Services\IdentityService;
use Modules\Identity\Application\Services\IdentitySessionService;
use Modules\Identity\Application\Services\LoginHistoryService;
use Modules\Identity\Application\Services\TeamMemberService;
use Modules\Identity\Application\Services\TeamService;
use Modules\Identity\Database\Seeders\IdentityDemoSeeder;
use Modules\Identity\Domain\Models\Identity;
use Modules\Identity\Domain\Repositories\Contracts\ApiTokenRepositoryInterface;
use Modules\Identity\Domain\Repositories\Contracts\ContactRepositoryInterface;
use Modules\Identity\Domain\Repositories\Contracts\DeviceRepositoryInterface;
use Modules\Identity\Domain\Repositories\Contracts\EmployeeProfileRepositoryInterface;
use Modules\Identity\Domain\Repositories\Contracts\IdentityRepositoryInterface;
use Modules\Identity\Domain\Repositories\Contracts\IdentitySessionRepositoryInterface;
use Modules\Identity\Domain\Repositories\Contracts\LoginHistoryRepositoryInterface;
use Modules\Identity\Domain\Repositories\Contracts\TeamMemberRepositoryInterface;
use Modules\Identity\Domain\Repositories\Contracts\TeamRepositoryInterface;
use Modules\Identity\Http\Controllers\Api\ApiTokenApiController;
use Modules\Identity\Http\Controllers\Api\ContactApiController;
use Modules\Identity\Http\Controllers\Api\DeviceApiController;
use Modules\Identity\Http\Controllers\Api\EmployeeProfileApiController;
use Modules\Identity\Http\Controllers\Api\IdentityApiController;
use Modules\Identity\Http\Controllers\Api\IdentitySessionApiController;
use Modules\Identity\Http\Controllers\Api\LoginHistoryApiController;
use Modules\Identity\Http\Controllers\Api\TeamApiController;
use Modules\Identity\Http\Controllers\Api\TeamMemberApiController;
use Modules\Identity\Http\Controllers\Web\IdentityCrudWebController;
use Modules\Identity\Http\Controllers\Web\IdentityDashboardWebController;
use Modules\Identity\Infrastructure\Persistence\EloquentApiTokenRepository;
use Modules\Identity\Infrastructure\Persistence\EloquentContactRepository;
use Modules\Identity\Infrastructure\Persistence\EloquentDeviceRepository;
use Modules\Identity\Infrastructure\Persistence\EloquentEmployeeProfileRepository;
use Modules\Identity\Infrastructure\Persistence\EloquentIdentityRepository;
use Modules\Identity\Infrastructure\Persistence\EloquentIdentitySessionRepository;
use Modules\Identity\Infrastructure\Persistence\EloquentLoginHistoryRepository;
use Modules\Identity\Infrastructure\Persistence\EloquentTeamMemberRepository;
use Modules\Identity\Infrastructure\Persistence\EloquentTeamRepository;
use Modules\Identity\Policies\ApiTokenPolicy;
use Modules\Identity\Policies\ContactPolicy;
use Modules\Identity\Policies\DevicePolicy;
use Modules\Identity\Policies\EmployeeProfilePolicy;
use Modules\Identity\Policies\IdentityPolicy;
use Modules\Identity\Policies\IdentitySessionPolicy;
use Modules\Identity\Policies\LoginHistoryPolicy;
use Modules\Identity\Policies\TeamMemberPolicy;
use Modules\Identity\Policies\TeamPolicy;

final class IdentityServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.identity', new ModuleInfo('Identity', '1.0.0'));

        $container->singleton(IdentityRepositoryInterface::class, EloquentIdentityRepository::class);
        $container->singleton(TeamRepositoryInterface::class, EloquentTeamRepository::class);
        $container->singleton(TeamMemberRepositoryInterface::class, EloquentTeamMemberRepository::class);
        $container->singleton(EmployeeProfileRepositoryInterface::class, EloquentEmployeeProfileRepository::class);
        $container->singleton(ContactRepositoryInterface::class, EloquentContactRepository::class);
        $container->singleton(DeviceRepositoryInterface::class, EloquentDeviceRepository::class);
        $container->singleton(IdentitySessionRepositoryInterface::class, EloquentIdentitySessionRepository::class);
        $container->singleton(LoginHistoryRepositoryInterface::class, EloquentLoginHistoryRepository::class);
        $container->singleton(ApiTokenRepositoryInterface::class, EloquentApiTokenRepository::class);

        $container->singleton(IdentityPlatformHooks::class, IdentityPlatformHooks::class);
        $container->singleton(IdentityService::class, IdentityService::class);
        $container->singleton(TeamService::class, TeamService::class);
        $container->singleton(TeamMemberService::class, TeamMemberService::class);
        $container->singleton(EmployeeProfileService::class, EmployeeProfileService::class);
        $container->singleton(ContactService::class, ContactService::class);
        $container->singleton(DeviceService::class, DeviceService::class);
        $container->singleton(IdentitySessionService::class, IdentitySessionService::class);
        $container->singleton(LoginHistoryService::class, LoginHistoryService::class);
        $container->singleton(ApiTokenService::class, ApiTokenService::class);

        $container->singleton(IdentityPolicy::class, IdentityPolicy::class);
        $container->singleton(TeamPolicy::class, TeamPolicy::class);
        $container->singleton(TeamMemberPolicy::class, TeamMemberPolicy::class);
        $container->singleton(EmployeeProfilePolicy::class, EmployeeProfilePolicy::class);
        $container->singleton(ContactPolicy::class, ContactPolicy::class);
        $container->singleton(DevicePolicy::class, DevicePolicy::class);
        $container->singleton(IdentitySessionPolicy::class, IdentitySessionPolicy::class);
        $container->singleton(LoginHistoryPolicy::class, LoginHistoryPolicy::class);
        $container->singleton(ApiTokenPolicy::class, ApiTokenPolicy::class);

        $container->singleton(IdentityApiController::class, IdentityApiController::class);
        $container->singleton(TeamApiController::class, TeamApiController::class);
        $container->singleton(TeamMemberApiController::class, TeamMemberApiController::class);
        $container->singleton(EmployeeProfileApiController::class, EmployeeProfileApiController::class);
        $container->singleton(ContactApiController::class, ContactApiController::class);
        $container->singleton(DeviceApiController::class, DeviceApiController::class);
        $container->singleton(IdentitySessionApiController::class, IdentitySessionApiController::class);
        $container->singleton(LoginHistoryApiController::class, LoginHistoryApiController::class);
        $container->singleton(ApiTokenApiController::class, ApiTokenApiController::class);

        $container->singleton(IdentityDashboardWebController::class, IdentityDashboardWebController::class);
        $container->singleton(IdentityCrudWebController::class, IdentityCrudWebController::class);
    }

    public function boot(ContainerInterface $container): void
    {
        $migrations = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Migrations';
        MigrationRunner::create(DatabaseBootstrap::capsule())->runIfNeeded([$migrations]);

        if ((getenv('APP_ENV') ?: 'production') !== 'production' && Identity::query()->count() === 0) {
            (new IdentityDemoSeeder())->run();
        }

        if (! $container->has(Router::class)) {
            return;
        }

        $router = $container->make(Router::class);
        $registrar = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'routes.php';

        if (is_callable($registrar)) {
            $registrar($router, $container);
        }
    }
}
