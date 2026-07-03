<?php
declare(strict_types=1);
namespace Modules\CRM\Providers;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\CRM\Http\Controllers\Api\CRMApiController;
use Modules\CRM\Http\Controllers\Web\CRMCrudWebController;
use Modules\CRM\Http\Controllers\Web\CRMDashboardWebController;
final class CRMServiceProvider extends ModuleServiceProvider {
    public function register(ContainerInterface $container): void {
        $container->instance('module.' . strtolower('CRM'), new ModuleInfo('CRM', '1.0.0'));
        $container->singleton(\Modules\CRM\Domain\Repositories\Contracts\LeadRepositoryInterface::class, \Modules\CRM\Infrastructure\Persistence\EloquentLeadRepository::class);
        $container->singleton(\Modules\CRM\Application\Services\LeadService::class, \Modules\CRM\Application\Services\LeadService::class);
        $container->singleton(\Modules\CRM\Domain\Repositories\Contracts\OpportunityRepositoryInterface::class, \Modules\CRM\Infrastructure\Persistence\EloquentOpportunityRepository::class);
        $container->singleton(\Modules\CRM\Application\Services\OpportunityService::class, \Modules\CRM\Application\Services\OpportunityService::class);
        $container->singleton(\Modules\CRM\Domain\Repositories\Contracts\CrmActivityRepositoryInterface::class, \Modules\CRM\Infrastructure\Persistence\EloquentCrmActivityRepository::class);
        $container->singleton(\Modules\CRM\Application\Services\CrmActivityService::class, \Modules\CRM\Application\Services\CrmActivityService::class);
        $container->singleton(\Modules\CRM\Policies\LeadPolicy::class, \Modules\CRM\Policies\LeadPolicy::class);
        $container->singleton(\Modules\CRM\Policies\OpportunityPolicy::class, \Modules\CRM\Policies\OpportunityPolicy::class);
        $container->singleton(\Modules\CRM\Policies\CrmActivityPolicy::class, \Modules\CRM\Policies\CrmActivityPolicy::class);

        $container->singleton(CRMApiController::class, CRMApiController::class);
        $container->singleton(CRMDashboardWebController::class, CRMDashboardWebController::class);
        $container->singleton(CRMCrudWebController::class, CRMCrudWebController::class);
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