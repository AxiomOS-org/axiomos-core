<?php

declare(strict_types=1);

use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Routing\Router;
use Modules\Authorization\Http\Controllers\Api\AuthorizationApiController;
use Modules\Authorization\Http\Controllers\Api\PermissionApiController;
use Modules\Authorization\Http\Controllers\Api\RoleApiController;
use Modules\Authorization\Http\Controllers\Web\SecurityCrudWebController;
use Modules\Authorization\Http\Controllers\Web\SecurityDashboardWebController;

return static function (Router $router, ContainerInterface $container): void {
    $roles = $container->make(RoleApiController::class);
    $permissions = $container->make(PermissionApiController::class);
    $authorization = $container->make(AuthorizationApiController::class);
    $dashboard = $container->make(SecurityDashboardWebController::class);
    $crud = $container->make(SecurityCrudWebController::class);

    $router->get('/api/security/roles', static fn (Request $request) => $roles->index($request));
    $router->post('/api/security/roles', static fn (Request $request) => $roles->store($request));
    $router->get('/api/security/roles/{id}', static fn (Request $request, string $id) => $roles->show($request, $id));
    $router->put('/api/security/roles/{id}', static fn (Request $request, string $id) => $roles->update($request, $id));
    $router->delete('/api/security/roles/{id}', static fn (Request $request, string $id) => $roles->destroy($request, $id));

    $router->get('/api/security/permissions', static fn (Request $request) => $permissions->index($request));
    $router->post('/api/security/permissions', static fn (Request $request) => $permissions->store($request));
    $router->get('/api/security/permissions/{id}', static fn (Request $request, string $id) => $permissions->show($request, $id));
    $router->put('/api/security/permissions/{id}', static fn (Request $request, string $id) => $permissions->update($request, $id));
    $router->delete('/api/security/permissions/{id}', static fn (Request $request, string $id) => $permissions->destroy($request, $id));

    $router->post('/api/security/roles/{id}/assign', static fn (Request $request, string $id) => $authorization->assign($request, $id));
    $router->post('/api/security/roles/{id}/revoke', static fn (Request $request, string $id) => $authorization->revoke($request, $id));
    $router->get('/api/security/users/{userId}/permissions', static fn (Request $request, string $userId) => $authorization->userPermissions($request, $userId));
    $router->get('/api/security/users/{userId}/roles', static fn (Request $request, string $userId) => $authorization->userRoles($request, $userId));

    $router->get('/security', static fn () => new HttpResponse('', 302, ['Location' => '/security/dashboard']));
    $router->get('/security/dashboard', static fn (Request $request) => $dashboard->index($request));
    $router->get('/security/roles', static fn (Request $request) => $crud->index($request, 'roles'));
    $router->get('/security/permissions', static fn (Request $request) => $crud->index($request, 'permissions'));
    $router->get('/security/sessions', static fn (Request $request) => $crud->index($request, 'sessions'));
    $router->get('/security/login-history', static fn (Request $request) => $crud->index($request, 'login-history'));
};
