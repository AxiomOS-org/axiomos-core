<?php

declare(strict_types=1);

namespace Modules\Users\Providers;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\Users\Application\Services\UserService;
use Modules\Users\Application\Services\UsersPlatformHooks;
use Modules\Users\Database\Seeders\UsersDemoSeeder;
use Modules\Users\Domain\Models\User;
use Modules\Users\Domain\Repositories\Contracts\UserRepositoryInterface;
use Modules\Users\Http\Controllers\Api\UserApiController;
use Modules\Users\Http\Controllers\Web\UserWebController;
use Modules\Users\Infrastructure\Persistence\EloquentUserRepository;
use Modules\Users\Policies\UserPolicy;

final class UsersServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.users', new ModuleInfo('Users', '1.0.0'));

        $container->singleton(UserRepositoryInterface::class, EloquentUserRepository::class);
        $container->singleton(UsersPlatformHooks::class, UsersPlatformHooks::class);
        $container->singleton(UserService::class, UserService::class);
        $container->singleton(UserPolicy::class, UserPolicy::class);
        $container->singleton(UserApiController::class, UserApiController::class);
        $container->singleton(UserWebController::class, UserWebController::class);
    }

    public function boot(ContainerInterface $container): void
    {
        $migrations = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Migrations';
        MigrationRunner::create(DatabaseBootstrap::capsule())->runIfNeeded([$migrations]);

        if ((getenv('APP_ENV') ?: 'production') !== 'production' && User::query()->count() === 0) {
            (new UsersDemoSeeder())->run();
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
