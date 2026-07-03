<?php
declare(strict_types=1);
namespace Modules\Budgeting\Providers;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\Budgeting\Http\Controllers\Api\BudgetingApiController;
use Modules\Budgeting\Http\Controllers\Web\BudgetingCrudWebController;
use Modules\Budgeting\Http\Controllers\Web\BudgetingDashboardWebController;
final class BudgetingServiceProvider extends ModuleServiceProvider {
    public function register(ContainerInterface $container): void {
        $container->instance('module.' . strtolower('Budgeting'), new ModuleInfo('Budgeting', '1.0.0'));
        $container->singleton(\Modules\Budgeting\Domain\Repositories\Contracts\BudgetVersionRepositoryInterface::class, \Modules\Budgeting\Infrastructure\Persistence\EloquentBudgetVersionRepository::class);
        $container->singleton(\Modules\Budgeting\Application\Services\BudgetVersionService::class, \Modules\Budgeting\Application\Services\BudgetVersionService::class);
        $container->singleton(\Modules\Budgeting\Domain\Repositories\Contracts\BudgetLineRepositoryInterface::class, \Modules\Budgeting\Infrastructure\Persistence\EloquentBudgetLineRepository::class);
        $container->singleton(\Modules\Budgeting\Application\Services\BudgetLineService::class, \Modules\Budgeting\Application\Services\BudgetLineService::class);
        $container->singleton(\Modules\Budgeting\Policies\BudgetVersionPolicy::class, \Modules\Budgeting\Policies\BudgetVersionPolicy::class);
        $container->singleton(\Modules\Budgeting\Policies\BudgetLinePolicy::class, \Modules\Budgeting\Policies\BudgetLinePolicy::class);

        $container->singleton(BudgetingApiController::class, BudgetingApiController::class);
        $container->singleton(BudgetingDashboardWebController::class, BudgetingDashboardWebController::class);
        $container->singleton(BudgetingCrudWebController::class, BudgetingCrudWebController::class);
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