<?php

declare(strict_types=1);

namespace Modules\Organization\Providers;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\Organization\Application\Services\BranchService;
use Modules\Organization\Application\Services\CompanyService;
use Modules\Organization\Application\Services\DepartmentService;
use Modules\Organization\Application\Services\OrganizationPlatformHooks;
use Modules\Organization\Application\Services\OrganizationService;
use Modules\Organization\Application\Support\SlugGenerator;
use Modules\Organization\Database\Seeders\OrganizationDemoSeeder;
use Modules\Organization\Domain\Models\Organization;
use Modules\Organization\Domain\Repositories\Contracts\BranchRepositoryInterface;
use Modules\Organization\Domain\Repositories\Contracts\CompanyRepositoryInterface;
use Modules\Organization\Domain\Repositories\Contracts\DepartmentRepositoryInterface;
use Modules\Organization\Domain\Repositories\Contracts\OrganizationRepositoryInterface;
use Modules\Organization\Infrastructure\Persistence\EloquentBranchRepository;
use Modules\Organization\Infrastructure\Persistence\EloquentCompanyRepository;
use Modules\Organization\Infrastructure\Persistence\EloquentDepartmentRepository;
use Modules\Organization\Infrastructure\Persistence\EloquentOrganizationRepository;
use Modules\Organization\Http\Controllers\Api\BranchApiController;
use Modules\Organization\Http\Controllers\Api\CompanyApiController;
use Modules\Organization\Http\Controllers\Api\DepartmentApiController;
use Modules\Organization\Http\Controllers\Api\OrganizationApiController;
use Modules\Organization\Http\Controllers\Web\BranchWebController;
use Modules\Organization\Http\Controllers\Web\CompanyWebController;
use Modules\Organization\Http\Controllers\Web\DepartmentWebController;
use Modules\Organization\Http\Controllers\Web\OrganizationWebController;
use Modules\Organization\Policies\BranchPolicy;
use Modules\Organization\Policies\CompanyPolicy;
use Modules\Organization\Policies\DepartmentPolicy;
use Modules\Organization\Policies\OrganizationPolicy;

/**
 * Boots the Organization foundation module: persistence, services, API and UI.
 */
final class OrganizationServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.organization', new ModuleInfo('Organization', '1.0.0'));

        $container->singleton(OrganizationRepositoryInterface::class, EloquentOrganizationRepository::class);
        $container->singleton(CompanyRepositoryInterface::class, EloquentCompanyRepository::class);
        $container->singleton(BranchRepositoryInterface::class, EloquentBranchRepository::class);
        $container->singleton(DepartmentRepositoryInterface::class, EloquentDepartmentRepository::class);

        $container->singleton(OrganizationPlatformHooks::class, OrganizationPlatformHooks::class);
        $container->singleton(SlugGenerator::class, SlugGenerator::class);
        $container->singleton(OrganizationService::class, OrganizationService::class);
        $container->singleton(CompanyService::class, CompanyService::class);
        $container->singleton(BranchService::class, BranchService::class);
        $container->singleton(DepartmentService::class, DepartmentService::class);

        $container->singleton(OrganizationPolicy::class, OrganizationPolicy::class);
        $container->singleton(CompanyPolicy::class, CompanyPolicy::class);
        $container->singleton(BranchPolicy::class, BranchPolicy::class);
        $container->singleton(DepartmentPolicy::class, DepartmentPolicy::class);

        $container->singleton(OrganizationApiController::class, OrganizationApiController::class);
        $container->singleton(CompanyApiController::class, CompanyApiController::class);
        $container->singleton(BranchApiController::class, BranchApiController::class);
        $container->singleton(DepartmentApiController::class, DepartmentApiController::class);

        $container->singleton(OrganizationWebController::class, OrganizationWebController::class);
        $container->singleton(CompanyWebController::class, CompanyWebController::class);
        $container->singleton(BranchWebController::class, BranchWebController::class);
        $container->singleton(DepartmentWebController::class, DepartmentWebController::class);
    }

    public function boot(ContainerInterface $container): void
    {
        $migrations = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Migrations';

        MigrationRunner::create(DatabaseBootstrap::capsule())->runIfNeeded([$migrations]);

        if ((getenv('APP_ENV') ?: 'production') !== 'production') {
            if (Organization::query()->count() === 0) {
                (new OrganizationDemoSeeder())->run();
            }
        }

        if (! $container->has(Router::class)) {
            return;
        }

        $router = $container->make(Router::class);
        $registrar = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'routes.php';

        if (is_callable($registrar)) {
            $registrar($router, $container);
        }
    }
}
