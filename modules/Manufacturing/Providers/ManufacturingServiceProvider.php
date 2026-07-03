<?php
declare(strict_types=1);
namespace Modules\Manufacturing\Providers;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\Manufacturing\Http\Controllers\Api\ManufacturingApiController;
use Modules\Manufacturing\Http\Controllers\Web\ManufacturingCrudWebController;
use Modules\Manufacturing\Http\Controllers\Web\ManufacturingDashboardWebController;
final class ManufacturingServiceProvider extends ModuleServiceProvider {
    public function register(ContainerInterface $container): void {
        $container->instance('module.' . strtolower('Manufacturing'), new ModuleInfo('Manufacturing', '1.0.0'));
        $container->singleton(\Modules\Manufacturing\Domain\Repositories\Contracts\BillOfMaterialRepositoryInterface::class, \Modules\Manufacturing\Infrastructure\Persistence\EloquentBillOfMaterialRepository::class);
        $container->singleton(\Modules\Manufacturing\Application\Services\BillOfMaterialService::class, \Modules\Manufacturing\Application\Services\BillOfMaterialService::class);
        $container->singleton(\Modules\Manufacturing\Domain\Repositories\Contracts\WorkOrderRepositoryInterface::class, \Modules\Manufacturing\Infrastructure\Persistence\EloquentWorkOrderRepository::class);
        $container->singleton(\Modules\Manufacturing\Application\Services\WorkOrderService::class, \Modules\Manufacturing\Application\Services\WorkOrderService::class);
        $container->singleton(\Modules\Manufacturing\Domain\Repositories\Contracts\ProductionRunRepositoryInterface::class, \Modules\Manufacturing\Infrastructure\Persistence\EloquentProductionRunRepository::class);
        $container->singleton(\Modules\Manufacturing\Application\Services\ProductionRunService::class, \Modules\Manufacturing\Application\Services\ProductionRunService::class);
        $container->singleton(\Modules\Manufacturing\Application\Services\ManufacturingPostingService::class, \Modules\Manufacturing\Application\Services\ManufacturingPostingService::class);
        $container->singleton(\Modules\Manufacturing\Policies\BillOfMaterialPolicy::class, \Modules\Manufacturing\Policies\BillOfMaterialPolicy::class);
        $container->singleton(\Modules\Manufacturing\Policies\WorkOrderPolicy::class, \Modules\Manufacturing\Policies\WorkOrderPolicy::class);
        $container->singleton(\Modules\Manufacturing\Policies\ProductionRunPolicy::class, \Modules\Manufacturing\Policies\ProductionRunPolicy::class);

        $container->singleton(ManufacturingApiController::class, ManufacturingApiController::class);
        $container->singleton(ManufacturingDashboardWebController::class, ManufacturingDashboardWebController::class);
        $container->singleton(ManufacturingCrudWebController::class, ManufacturingCrudWebController::class);
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