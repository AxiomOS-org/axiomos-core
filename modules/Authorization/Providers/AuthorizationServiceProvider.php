<?php

declare(strict_types=1);

namespace Modules\Authorization\Providers;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\Authorization\Application\Services\AuthorizationPlatformHooks;
use Modules\Authorization\Application\Services\AuthorizationService;
use Modules\Authorization\Application\Services\LoginSecurityService;
use Modules\Authorization\Application\Services\PermissionService;
use Modules\Authorization\Application\Services\PolicyEnforcementService;
use Modules\Authorization\Application\Services\RbacCacheService;
use Modules\Authorization\Application\Services\RoleService;
use Modules\Authorization\Database\Seeders\AuthorizationDemoSeeder;
use Modules\Authorization\Domain\Models\AuthorizationRole;
use Modules\Authorization\Http\Controllers\Api\AuthorizationApiController;
use Modules\Authorization\Http\Controllers\Api\PermissionApiController;
use Modules\Authorization\Http\Controllers\Api\RoleApiController;
use Modules\Authorization\Http\Controllers\Web\SecurityCrudWebController;
use Modules\Authorization\Http\Controllers\Web\SecurityDashboardWebController;
use Modules\Authorization\Policies\AuthorizationRolePolicy;

final class AuthorizationServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.authorization', new ModuleInfo('Authorization', '1.0.0'));

        $container->singleton(RbacCacheService::class, RbacCacheService::class);
        $container->singleton(AuthorizationPlatformHooks::class, AuthorizationPlatformHooks::class);
        $container->singleton(RoleService::class, RoleService::class);
        $container->singleton(PermissionService::class, PermissionService::class);
        $container->singleton(AuthorizationService::class, AuthorizationService::class);
        $container->singleton(PolicyEnforcementService::class, PolicyEnforcementService::class);
        $container->singleton(LoginSecurityService::class, LoginSecurityService::class);
        $container->singleton(AuthorizationRolePolicy::class, AuthorizationRolePolicy::class);

        $container->singleton(RoleApiController::class, RoleApiController::class);
        $container->singleton(PermissionApiController::class, PermissionApiController::class);
        $container->singleton(AuthorizationApiController::class, AuthorizationApiController::class);
        $container->singleton(SecurityDashboardWebController::class, SecurityDashboardWebController::class);
        $container->singleton(SecurityCrudWebController::class, SecurityCrudWebController::class);
    }

    public function boot(ContainerInterface $container): void
    {
        $migrations = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Migrations';
        MigrationRunner::create(DatabaseBootstrap::capsule())->runIfNeeded([$migrations]);

        if ((getenv('APP_ENV') ?: 'production') !== 'production' && AuthorizationRole::query()->count() === 0) {
            (new AuthorizationDemoSeeder())->run();
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
