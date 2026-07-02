<?php

declare(strict_types=1);

use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Organization\Http\Controllers\Api\BranchApiController;
use Modules\Organization\Http\Controllers\Api\CompanyApiController;
use Modules\Organization\Http\Controllers\Api\DepartmentApiController;
use Modules\Organization\Http\Controllers\Api\OrganizationApiController;
use Modules\Organization\Http\Controllers\Web\BranchWebController;
use Modules\Organization\Http\Controllers\Web\CompanyWebController;
use Modules\Organization\Http\Controllers\Web\DepartmentWebController;
use Modules\Organization\Http\Controllers\Web\OrganizationWebController;

return static function (Router $router, ContainerInterface $container): void {
    $organizations = $container->make(OrganizationApiController::class);
    $companies = $container->make(CompanyApiController::class);
    $branches = $container->make(BranchApiController::class);
    $departments = $container->make(DepartmentApiController::class);

    $router->get('/api/organizations', static fn (Request $request) => $organizations->index($request));
    $router->post('/api/organizations', static fn (Request $request) => $organizations->store($request));
    $router->get('/api/organizations/{id}', static fn (Request $request, string $id) => $organizations->show($request, $id));
    $router->put('/api/organizations/{id}', static fn (Request $request, string $id) => $organizations->update($request, $id));
    $router->delete('/api/organizations/{id}', static fn (Request $request, string $id) => $organizations->destroy($request, $id));

    $router->get('/api/companies', static fn (Request $request) => $companies->index($request));
    $router->post('/api/companies', static fn (Request $request) => $companies->store($request));
    $router->get('/api/companies/{id}', static fn (Request $request, string $id) => $companies->show($request, $id));
    $router->put('/api/companies/{id}', static fn (Request $request, string $id) => $companies->update($request, $id));
    $router->delete('/api/companies/{id}', static fn (Request $request, string $id) => $companies->destroy($request, $id));

    $router->get('/api/branches', static fn (Request $request) => $branches->index($request));
    $router->post('/api/branches', static fn (Request $request) => $branches->store($request));
    $router->get('/api/branches/{id}', static fn (Request $request, string $id) => $branches->show($request, $id));
    $router->put('/api/branches/{id}', static fn (Request $request, string $id) => $branches->update($request, $id));
    $router->delete('/api/branches/{id}', static fn (Request $request, string $id) => $branches->destroy($request, $id));

    $router->get('/api/departments', static fn (Request $request) => $departments->index($request));
    $router->post('/api/departments', static fn (Request $request) => $departments->store($request));
    $router->get('/api/departments/{id}', static fn (Request $request, string $id) => $departments->show($request, $id));
    $router->put('/api/departments/{id}', static fn (Request $request, string $id) => $departments->update($request, $id));
    $router->delete('/api/departments/{id}', static fn (Request $request, string $id) => $departments->destroy($request, $id));

    $orgWeb = $container->make(OrganizationWebController::class);
    $companyWeb = $container->make(CompanyWebController::class);
    $branchWeb = $container->make(BranchWebController::class);
    $departmentWeb = $container->make(DepartmentWebController::class);

    $router->get('/organizations', static fn (Request $request) => $orgWeb->index($request));
    $router->get('/companies', static fn (Request $request) => $companyWeb->index($request));
    $router->get('/branches', static fn (Request $request) => $branchWeb->index($request));
    $router->get('/departments', static fn (Request $request) => $departmentWeb->index($request));
};
