<?php
declare(strict_types=1);
namespace Modules\Reporting\Providers;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\Reporting\Http\Controllers\Api\ReportingApiController;
use Modules\Reporting\Http\Controllers\Web\ReportingCrudWebController;
use Modules\Reporting\Http\Controllers\Web\ReportingDashboardWebController;
final class ReportingServiceProvider extends ModuleServiceProvider {
    public function register(ContainerInterface $container): void {
        $container->instance('module.' . strtolower('Reporting'), new ModuleInfo('Reporting', '1.0.0'));
        $container->singleton(\Modules\Reporting\Domain\Repositories\Contracts\ReportDefinitionRepositoryInterface::class, \Modules\Reporting\Infrastructure\Persistence\EloquentReportDefinitionRepository::class);
        $container->singleton(\Modules\Reporting\Application\Services\ReportDefinitionService::class, \Modules\Reporting\Application\Services\ReportDefinitionService::class);
        $container->singleton(\Modules\Reporting\Domain\Repositories\Contracts\ReportSnapshotRepositoryInterface::class, \Modules\Reporting\Infrastructure\Persistence\EloquentReportSnapshotRepository::class);
        $container->singleton(\Modules\Reporting\Application\Services\ReportSnapshotService::class, \Modules\Reporting\Application\Services\ReportSnapshotService::class);
        $container->singleton(\Modules\Reporting\Application\Services\ReportingDashboardService::class, \Modules\Reporting\Application\Services\ReportingDashboardService::class);
        $container->singleton(\Modules\Reporting\Policies\ReportDefinitionPolicy::class, \Modules\Reporting\Policies\ReportDefinitionPolicy::class);
        $container->singleton(\Modules\Reporting\Policies\ReportSnapshotPolicy::class, \Modules\Reporting\Policies\ReportSnapshotPolicy::class);

        $container->singleton(ReportingApiController::class, ReportingApiController::class);
        $container->singleton(ReportingDashboardWebController::class, ReportingDashboardWebController::class);
        $container->singleton(ReportingCrudWebController::class, ReportingCrudWebController::class);
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