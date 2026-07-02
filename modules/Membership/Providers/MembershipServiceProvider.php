<?php

declare(strict_types=1);

namespace Modules\Membership\Providers;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\Membership\Application\Services\MembershipPlatformHooks;
use Modules\Membership\Application\Services\MembershipService;
use Modules\Membership\Database\Seeders\MembershipDemoSeeder;
use Modules\Membership\Domain\Models\Membership;
use Modules\Membership\Domain\Repositories\Contracts\MembershipRepositoryInterface;
use Modules\Membership\Http\Controllers\Api\MembershipApiController;
use Modules\Membership\Http\Controllers\Web\MembershipWebController;
use Modules\Membership\Infrastructure\Persistence\EloquentMembershipRepository;
use Modules\Membership\Policies\MembershipPolicy;

final class MembershipServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.membership', new ModuleInfo('Membership', '1.0.0'));

        $container->singleton(MembershipRepositoryInterface::class, EloquentMembershipRepository::class);
        $container->singleton(MembershipPlatformHooks::class, MembershipPlatformHooks::class);
        $container->singleton(MembershipService::class, MembershipService::class);
        $container->singleton(MembershipPolicy::class, MembershipPolicy::class);
        $container->singleton(MembershipApiController::class, MembershipApiController::class);
        $container->singleton(MembershipWebController::class, MembershipWebController::class);
    }

    public function boot(ContainerInterface $container): void
    {
        $migrations = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Migrations';
        MigrationRunner::create(DatabaseBootstrap::capsule())->runIfNeeded([$migrations]);

        if ((getenv('APP_ENV') ?: 'production') !== 'production' && Membership::query()->count() === 0) {
            (new MembershipDemoSeeder())->run();
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
