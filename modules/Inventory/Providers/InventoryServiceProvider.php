<?php
declare(strict_types=1);
namespace Modules\Inventory\Providers;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\Inventory\Http\Controllers\Api\InventoryApiController;
use Modules\Inventory\Http\Controllers\Web\InventoryCrudWebController;
use Modules\Inventory\Http\Controllers\Web\InventoryDashboardWebController;
final class InventoryServiceProvider extends ModuleServiceProvider {
    public function register(ContainerInterface $container): void {
        $container->instance('module.' . strtolower('Inventory'), new ModuleInfo('Inventory', '1.0.0'));
        $container->singleton(\Modules\Inventory\Domain\Repositories\Contracts\WarehouseRepositoryInterface::class, \Modules\Inventory\Infrastructure\Persistence\EloquentWarehouseRepository::class);
        $container->singleton(\Modules\Inventory\Application\Services\WarehouseService::class, \Modules\Inventory\Application\Services\WarehouseService::class);
        $container->singleton(\Modules\Inventory\Domain\Repositories\Contracts\ItemRepositoryInterface::class, \Modules\Inventory\Infrastructure\Persistence\EloquentItemRepository::class);
        $container->singleton(\Modules\Inventory\Application\Services\ItemService::class, \Modules\Inventory\Application\Services\ItemService::class);
        $container->singleton(\Modules\Inventory\Domain\Repositories\Contracts\StockMovementRepositoryInterface::class, \Modules\Inventory\Infrastructure\Persistence\EloquentStockMovementRepository::class);
        $container->singleton(\Modules\Inventory\Application\Services\StockMovementService::class, \Modules\Inventory\Application\Services\StockMovementService::class);
        $container->singleton(\Modules\Inventory\Policies\WarehousePolicy::class, \Modules\Inventory\Policies\WarehousePolicy::class);
        $container->singleton(\Modules\Inventory\Policies\ItemPolicy::class, \Modules\Inventory\Policies\ItemPolicy::class);
        $container->singleton(\Modules\Inventory\Policies\StockMovementPolicy::class, \Modules\Inventory\Policies\StockMovementPolicy::class);

        $container->singleton(InventoryApiController::class, InventoryApiController::class);
        $container->singleton(InventoryDashboardWebController::class, InventoryDashboardWebController::class);
        $container->singleton(InventoryCrudWebController::class, InventoryCrudWebController::class);
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