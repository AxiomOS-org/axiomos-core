<?php
declare(strict_types=1);
namespace Modules\HR\Providers;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\HR\Http\Controllers\Api\HRApiController;
use Modules\HR\Http\Controllers\Web\HRCrudWebController;
use Modules\HR\Http\Controllers\Web\HRDashboardWebController;
final class HRServiceProvider extends ModuleServiceProvider {
    public function register(ContainerInterface $container): void {
        $container->instance('module.' . strtolower('HR'), new ModuleInfo('HR', '1.0.0'));
        $container->singleton(\Modules\HR\Domain\Repositories\Contracts\EmployeeRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\EloquentEmployeeRepository::class);
        $container->singleton(\Modules\HR\Application\Services\EmployeeService::class, \Modules\HR\Application\Services\EmployeeService::class);
        $container->singleton(\Modules\HR\Domain\Repositories\Contracts\AttendanceRecordRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\EloquentAttendanceRecordRepository::class);
        $container->singleton(\Modules\HR\Application\Services\AttendanceRecordService::class, \Modules\HR\Application\Services\AttendanceRecordService::class);
        $container->singleton(\Modules\HR\Domain\Repositories\Contracts\PayrollRunRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\EloquentPayrollRunRepository::class);
        $container->singleton(\Modules\HR\Application\Services\PayrollRunService::class, \Modules\HR\Application\Services\PayrollRunService::class);
        $container->singleton(\Modules\HR\Application\Services\HRPostingService::class, \Modules\HR\Application\Services\HRPostingService::class);
        $container->singleton(\Modules\HR\Policies\EmployeePolicy::class, \Modules\HR\Policies\EmployeePolicy::class);
        $container->singleton(\Modules\HR\Policies\AttendanceRecordPolicy::class, \Modules\HR\Policies\AttendanceRecordPolicy::class);
        $container->singleton(\Modules\HR\Policies\PayrollRunPolicy::class, \Modules\HR\Policies\PayrollRunPolicy::class);

        $container->singleton(HRApiController::class, HRApiController::class);
        $container->singleton(HRDashboardWebController::class, HRDashboardWebController::class);
        $container->singleton(HRCrudWebController::class, HRCrudWebController::class);
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