<?php
declare(strict_types=1);
namespace Modules\Projects\Providers;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\Projects\Http\Controllers\Api\ProjectsApiController;
use Modules\Projects\Http\Controllers\Web\ProjectsCrudWebController;
use Modules\Projects\Http\Controllers\Web\ProjectsDashboardWebController;
final class ProjectsServiceProvider extends ModuleServiceProvider {
    public function register(ContainerInterface $container): void {
        $container->instance('module.' . strtolower('Projects'), new ModuleInfo('Projects', '1.0.0'));
        $container->singleton(\Modules\Projects\Domain\Repositories\Contracts\ProjectRepositoryInterface::class, \Modules\Projects\Infrastructure\Persistence\EloquentProjectRepository::class);
        $container->singleton(\Modules\Projects\Application\Services\ProjectService::class, \Modules\Projects\Application\Services\ProjectService::class);
        $container->singleton(\Modules\Projects\Domain\Repositories\Contracts\ProjectTaskRepositoryInterface::class, \Modules\Projects\Infrastructure\Persistence\EloquentProjectTaskRepository::class);
        $container->singleton(\Modules\Projects\Application\Services\ProjectTaskService::class, \Modules\Projects\Application\Services\ProjectTaskService::class);
        $container->singleton(\Modules\Projects\Domain\Repositories\Contracts\TimesheetRepositoryInterface::class, \Modules\Projects\Infrastructure\Persistence\EloquentTimesheetRepository::class);
        $container->singleton(\Modules\Projects\Application\Services\TimesheetService::class, \Modules\Projects\Application\Services\TimesheetService::class);
        $container->singleton(\Modules\Projects\Policies\ProjectPolicy::class, \Modules\Projects\Policies\ProjectPolicy::class);
        $container->singleton(\Modules\Projects\Policies\ProjectTaskPolicy::class, \Modules\Projects\Policies\ProjectTaskPolicy::class);
        $container->singleton(\Modules\Projects\Policies\TimesheetPolicy::class, \Modules\Projects\Policies\TimesheetPolicy::class);

        $container->singleton(ProjectsApiController::class, ProjectsApiController::class);
        $container->singleton(ProjectsDashboardWebController::class, ProjectsDashboardWebController::class);
        $container->singleton(ProjectsCrudWebController::class, ProjectsCrudWebController::class);
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