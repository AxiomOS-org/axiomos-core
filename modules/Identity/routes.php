<?php

declare(strict_types=1);

use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Identity\Http\Controllers\Api\ApiTokenApiController;
use Modules\Identity\Http\Controllers\Api\ContactApiController;
use Modules\Identity\Http\Controllers\Api\DeviceApiController;
use Modules\Identity\Http\Controllers\Api\EmployeeProfileApiController;
use Modules\Identity\Http\Controllers\Api\IdentityApiController;
use Modules\Identity\Http\Controllers\Api\IdentitySessionApiController;
use Modules\Identity\Http\Controllers\Api\LoginHistoryApiController;
use Modules\Identity\Http\Controllers\Api\TeamApiController;
use Modules\Identity\Http\Controllers\Api\TeamMemberApiController;
use Modules\Identity\Http\Controllers\Web\IdentityCrudWebController;
use Modules\Identity\Http\Controllers\Web\IdentityDashboardWebController;

return static function (Router $router, ContainerInterface $container): void {
    $identities = $container->make(IdentityApiController::class);
    $teams = $container->make(TeamApiController::class);
    $teamMembers = $container->make(TeamMemberApiController::class);
    $employeeProfiles = $container->make(EmployeeProfileApiController::class);
    $contacts = $container->make(ContactApiController::class);
    $devices = $container->make(DeviceApiController::class);
    $sessions = $container->make(IdentitySessionApiController::class);
    $loginHistory = $container->make(LoginHistoryApiController::class);
    $tokens = $container->make(ApiTokenApiController::class);

    $router->get('/api/identities', static fn (Request $request) => $identities->index($request));
    $router->post('/api/identities', static fn (Request $request) => $identities->store($request));
    $router->get('/api/identities/{id}', static fn (Request $request, string $id) => $identities->show($request, $id));
    $router->put('/api/identities/{id}', static fn (Request $request, string $id) => $identities->update($request, $id));
    $router->delete('/api/identities/{id}', static fn (Request $request, string $id) => $identities->destroy($request, $id));

    $router->get('/api/teams', static fn (Request $request) => $teams->index($request));
    $router->post('/api/teams', static fn (Request $request) => $teams->store($request));
    $router->get('/api/teams/{id}', static fn (Request $request, string $id) => $teams->show($request, $id));
    $router->put('/api/teams/{id}', static fn (Request $request, string $id) => $teams->update($request, $id));
    $router->delete('/api/teams/{id}', static fn (Request $request, string $id) => $teams->destroy($request, $id));

    $router->get('/api/team-members', static fn (Request $request) => $teamMembers->index($request));
    $router->post('/api/team-members', static fn (Request $request) => $teamMembers->store($request));
    $router->get('/api/team-members/{id}', static fn (Request $request, string $id) => $teamMembers->show($request, $id));
    $router->put('/api/team-members/{id}', static fn (Request $request, string $id) => $teamMembers->update($request, $id));
    $router->delete('/api/team-members/{id}', static fn (Request $request, string $id) => $teamMembers->destroy($request, $id));

    $router->get('/api/employee-profiles', static fn (Request $request) => $employeeProfiles->index($request));
    $router->post('/api/employee-profiles', static fn (Request $request) => $employeeProfiles->store($request));
    $router->get('/api/employee-profiles/{id}', static fn (Request $request, string $id) => $employeeProfiles->show($request, $id));
    $router->put('/api/employee-profiles/{id}', static fn (Request $request, string $id) => $employeeProfiles->update($request, $id));
    $router->delete('/api/employee-profiles/{id}', static fn (Request $request, string $id) => $employeeProfiles->destroy($request, $id));

    $router->get('/api/contacts', static fn (Request $request) => $contacts->index($request));
    $router->post('/api/contacts', static fn (Request $request) => $contacts->store($request));
    $router->get('/api/contacts/{id}', static fn (Request $request, string $id) => $contacts->show($request, $id));
    $router->put('/api/contacts/{id}', static fn (Request $request, string $id) => $contacts->update($request, $id));
    $router->delete('/api/contacts/{id}', static fn (Request $request, string $id) => $contacts->destroy($request, $id));

    $router->get('/api/devices', static fn (Request $request) => $devices->index($request));
    $router->post('/api/devices', static fn (Request $request) => $devices->store($request));
    $router->get('/api/devices/{id}', static fn (Request $request, string $id) => $devices->show($request, $id));
    $router->put('/api/devices/{id}', static fn (Request $request, string $id) => $devices->update($request, $id));
    $router->delete('/api/devices/{id}', static fn (Request $request, string $id) => $devices->destroy($request, $id));

    $router->get('/api/identity-sessions', static fn (Request $request) => $sessions->index($request));
    $router->post('/api/identity-sessions', static fn (Request $request) => $sessions->store($request));
    $router->get('/api/identity-sessions/{id}', static fn (Request $request, string $id) => $sessions->show($request, $id));
    $router->put('/api/identity-sessions/{id}', static fn (Request $request, string $id) => $sessions->update($request, $id));
    $router->delete('/api/identity-sessions/{id}', static fn (Request $request, string $id) => $sessions->destroy($request, $id));

    $router->get('/api/login-history', static fn (Request $request) => $loginHistory->index($request));
    $router->post('/api/login-history', static fn (Request $request) => $loginHistory->store($request));
    $router->get('/api/login-history/{id}', static fn (Request $request, string $id) => $loginHistory->show($request, $id));
    $router->put('/api/login-history/{id}', static fn (Request $request, string $id) => $loginHistory->update($request, $id));
    $router->delete('/api/login-history/{id}', static fn (Request $request, string $id) => $loginHistory->destroy($request, $id));

    $router->get('/api/api-tokens', static fn (Request $request) => $tokens->index($request));
    $router->post('/api/api-tokens', static fn (Request $request) => $tokens->store($request));
    $router->get('/api/api-tokens/{id}', static fn (Request $request, string $id) => $tokens->show($request, $id));
    $router->put('/api/api-tokens/{id}', static fn (Request $request, string $id) => $tokens->update($request, $id));
    $router->delete('/api/api-tokens/{id}', static fn (Request $request, string $id) => $tokens->destroy($request, $id));

    $dashboard = $container->make(IdentityDashboardWebController::class);
    $crud = $container->make(IdentityCrudWebController::class);

    $router->get('/identity', static fn (Request $request) => $dashboard->index($request));
    $router->get('/identity/dashboard', static fn (Request $request) => $dashboard->index($request));
    $router->get('/identity/identities', static fn (Request $request) => $crud->index($request, 'identities'));
    $router->get('/identity/teams', static fn (Request $request) => $crud->index($request, 'teams'));
    $router->get('/identity/team-members', static fn (Request $request) => $crud->index($request, 'team-members'));
    $router->get('/identity/employee-profiles', static fn (Request $request) => $crud->index($request, 'employee-profiles'));
    $router->get('/identity/contacts', static fn (Request $request) => $crud->index($request, 'contacts'));
    $router->get('/identity/devices', static fn (Request $request) => $crud->index($request, 'devices'));
    $router->get('/identity/identity-sessions', static fn (Request $request) => $crud->index($request, 'identity-sessions'));
    $router->get('/identity/login-history', static fn (Request $request) => $crud->index($request, 'login-history'));
    $router->get('/identity/api-tokens', static fn (Request $request) => $crud->index($request, 'api-tokens'));
};
