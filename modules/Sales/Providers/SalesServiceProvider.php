<?php
declare(strict_types=1);
namespace Modules\Sales\Providers;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\Sales\Http\Controllers\Api\SalesApiController;
use Modules\Sales\Http\Controllers\Web\SalesCrudWebController;
use Modules\Sales\Http\Controllers\Web\SalesDashboardWebController;
final class SalesServiceProvider extends ModuleServiceProvider {
    public function register(ContainerInterface $container): void {
        $container->instance('module.' . strtolower('Sales'), new ModuleInfo('Sales', '1.0.0'));
        $container->singleton(\Modules\Sales\Domain\Repositories\Contracts\CustomerRepositoryInterface::class, \Modules\Sales\Infrastructure\Persistence\EloquentCustomerRepository::class);
        $container->singleton(\Modules\Sales\Application\Services\CustomerService::class, \Modules\Sales\Application\Services\CustomerService::class);
        $container->singleton(\Modules\Sales\Domain\Repositories\Contracts\SalesOrderRepositoryInterface::class, \Modules\Sales\Infrastructure\Persistence\EloquentSalesOrderRepository::class);
        $container->singleton(\Modules\Sales\Application\Services\SalesOrderService::class, \Modules\Sales\Application\Services\SalesOrderService::class);
        $container->singleton(\Modules\Sales\Domain\Repositories\Contracts\SalesInvoiceRepositoryInterface::class, \Modules\Sales\Infrastructure\Persistence\EloquentSalesInvoiceRepository::class);
        $container->singleton(\Modules\Sales\Application\Services\SalesInvoiceService::class, \Modules\Sales\Application\Services\SalesInvoiceService::class);
        $container->singleton(\Modules\Sales\Application\Services\SalesPostingService::class, \Modules\Sales\Application\Services\SalesPostingService::class);
        $container->singleton(\Modules\Sales\Policies\CustomerPolicy::class, \Modules\Sales\Policies\CustomerPolicy::class);
        $container->singleton(\Modules\Sales\Policies\SalesOrderPolicy::class, \Modules\Sales\Policies\SalesOrderPolicy::class);
        $container->singleton(\Modules\Sales\Policies\SalesInvoicePolicy::class, \Modules\Sales\Policies\SalesInvoicePolicy::class);

        $container->singleton(SalesApiController::class, SalesApiController::class);
        $container->singleton(SalesDashboardWebController::class, SalesDashboardWebController::class);
        $container->singleton(SalesCrudWebController::class, SalesCrudWebController::class);
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