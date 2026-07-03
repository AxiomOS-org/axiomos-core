<?php
declare(strict_types=1);
namespace Modules\FixedAssets\Providers;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\FixedAssets\Http\Controllers\Api\FixedAssetsApiController;
use Modules\FixedAssets\Http\Controllers\Web\FixedAssetsCrudWebController;
use Modules\FixedAssets\Http\Controllers\Web\FixedAssetsDashboardWebController;
final class FixedAssetsServiceProvider extends ModuleServiceProvider {
    public function register(ContainerInterface $container): void {
        $container->instance('module.' . strtolower('FixedAssets'), new ModuleInfo('FixedAssets', '1.0.0'));
        $container->singleton(\Modules\FixedAssets\Domain\Repositories\Contracts\FixedAssetRepositoryInterface::class, \Modules\FixedAssets\Infrastructure\Persistence\EloquentFixedAssetRepository::class);
        $container->singleton(\Modules\FixedAssets\Application\Services\FixedAssetService::class, \Modules\FixedAssets\Application\Services\FixedAssetService::class);
        $container->singleton(\Modules\FixedAssets\Domain\Repositories\Contracts\DepreciationRunRepositoryInterface::class, \Modules\FixedAssets\Infrastructure\Persistence\EloquentDepreciationRunRepository::class);
        $container->singleton(\Modules\FixedAssets\Application\Services\DepreciationRunService::class, \Modules\FixedAssets\Application\Services\DepreciationRunService::class);
        $container->singleton(\Modules\FixedAssets\Application\Services\FixedAssetsPostingService::class, \Modules\FixedAssets\Application\Services\FixedAssetsPostingService::class);
        $container->singleton(\Modules\FixedAssets\Policies\FixedAssetPolicy::class, \Modules\FixedAssets\Policies\FixedAssetPolicy::class);
        $container->singleton(\Modules\FixedAssets\Policies\DepreciationRunPolicy::class, \Modules\FixedAssets\Policies\DepreciationRunPolicy::class);

        $container->singleton(FixedAssetsApiController::class, FixedAssetsApiController::class);
        $container->singleton(FixedAssetsDashboardWebController::class, FixedAssetsDashboardWebController::class);
        $container->singleton(FixedAssetsCrudWebController::class, FixedAssetsCrudWebController::class);
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