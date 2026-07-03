<?php
declare(strict_types=1);
namespace Modules\POS\Providers;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\POS\Http\Controllers\Api\POSApiController;
use Modules\POS\Http\Controllers\Web\POSCrudWebController;
use Modules\POS\Http\Controllers\Web\POSDashboardWebController;
final class POSServiceProvider extends ModuleServiceProvider {
    public function register(ContainerInterface $container): void {
        $container->instance('module.' . strtolower('POS'), new ModuleInfo('POS', '1.0.0'));
        $container->singleton(\Modules\POS\Domain\Repositories\Contracts\PosTerminalRepositoryInterface::class, \Modules\POS\Infrastructure\Persistence\EloquentPosTerminalRepository::class);
        $container->singleton(\Modules\POS\Application\Services\PosTerminalService::class, \Modules\POS\Application\Services\PosTerminalService::class);
        $container->singleton(\Modules\POS\Domain\Repositories\Contracts\PosSessionRepositoryInterface::class, \Modules\POS\Infrastructure\Persistence\EloquentPosSessionRepository::class);
        $container->singleton(\Modules\POS\Application\Services\PosSessionService::class, \Modules\POS\Application\Services\PosSessionService::class);
        $container->singleton(\Modules\POS\Domain\Repositories\Contracts\PosOrderRepositoryInterface::class, \Modules\POS\Infrastructure\Persistence\EloquentPosOrderRepository::class);
        $container->singleton(\Modules\POS\Application\Services\PosOrderService::class, \Modules\POS\Application\Services\PosOrderService::class);
        $container->singleton(\Modules\POS\Application\Services\POSPostingService::class, \Modules\POS\Application\Services\POSPostingService::class);
        $container->singleton(\Modules\POS\Policies\PosTerminalPolicy::class, \Modules\POS\Policies\PosTerminalPolicy::class);
        $container->singleton(\Modules\POS\Policies\PosSessionPolicy::class, \Modules\POS\Policies\PosSessionPolicy::class);
        $container->singleton(\Modules\POS\Policies\PosOrderPolicy::class, \Modules\POS\Policies\PosOrderPolicy::class);

        $container->singleton(POSApiController::class, POSApiController::class);
        $container->singleton(POSDashboardWebController::class, POSDashboardWebController::class);
        $container->singleton(POSCrudWebController::class, POSCrudWebController::class);
    }
    public function boot(ContainerInterface $container): void {
        $migrations = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Migrations';
        MigrationRunner::create(DatabaseBootstrap::capsule())->runIfNeeded([$migrations]);
        if (! $container->has(Router::class)) { return; }
        $router = $container->make(Router::class);
        $registrar = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'routes.php';
        if (is_callable($registrar)) { $registrar($router, $container); }
    }
}