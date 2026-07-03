<?php
declare(strict_types=1);
namespace Modules\Purchase\Providers;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\Purchase\Http\Controllers\Api\PurchaseApiController;
use Modules\Purchase\Http\Controllers\Web\PurchaseCrudWebController;
use Modules\Purchase\Http\Controllers\Web\PurchaseDashboardWebController;
final class PurchaseServiceProvider extends ModuleServiceProvider {
    public function register(ContainerInterface $container): void {
        $container->instance('module.' . strtolower('Purchase'), new ModuleInfo('Purchase', '1.0.0'));
        $container->singleton(\Modules\Purchase\Domain\Repositories\Contracts\VendorRepositoryInterface::class, \Modules\Purchase\Infrastructure\Persistence\EloquentVendorRepository::class);
        $container->singleton(\Modules\Purchase\Application\Services\VendorService::class, \Modules\Purchase\Application\Services\VendorService::class);
        $container->singleton(\Modules\Purchase\Domain\Repositories\Contracts\PurchaseOrderRepositoryInterface::class, \Modules\Purchase\Infrastructure\Persistence\EloquentPurchaseOrderRepository::class);
        $container->singleton(\Modules\Purchase\Application\Services\PurchaseOrderService::class, \Modules\Purchase\Application\Services\PurchaseOrderService::class);
        $container->singleton(\Modules\Purchase\Domain\Repositories\Contracts\PurchaseBillRepositoryInterface::class, \Modules\Purchase\Infrastructure\Persistence\EloquentPurchaseBillRepository::class);
        $container->singleton(\Modules\Purchase\Application\Services\PurchaseBillService::class, \Modules\Purchase\Application\Services\PurchaseBillService::class);
        $container->singleton(\Modules\Purchase\Application\Services\PurchasePostingService::class, \Modules\Purchase\Application\Services\PurchasePostingService::class);
        $container->singleton(\Modules\Purchase\Policies\VendorPolicy::class, \Modules\Purchase\Policies\VendorPolicy::class);
        $container->singleton(\Modules\Purchase\Policies\PurchaseOrderPolicy::class, \Modules\Purchase\Policies\PurchaseOrderPolicy::class);
        $container->singleton(\Modules\Purchase\Policies\PurchaseBillPolicy::class, \Modules\Purchase\Policies\PurchaseBillPolicy::class);

        $container->singleton(PurchaseApiController::class, PurchaseApiController::class);
        $container->singleton(PurchaseDashboardWebController::class, PurchaseDashboardWebController::class);
        $container->singleton(PurchaseCrudWebController::class, PurchaseCrudWebController::class);
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