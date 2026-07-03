<?php
declare(strict_types=1);
use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Projects\Http\Controllers\Api\ProjectsApiController;
use Modules\Projects\Http\Controllers\Web\ProjectsCrudWebController;
use Modules\Projects\Http\Controllers\Web\ProjectsDashboardWebController;
return static function (Router $router, ContainerInterface $container): void {
    $api = $container->make(ProjectsApiController::class);
    $router->get('/api/projects/project', static fn(Request $request) => $api->project($request));
    $router->post('/api/projects/project', static fn(Request $request) => $api->project($request));
    $router->patch('/api/projects/project', static fn(Request $request) => $api->project($request));
    $router->get('/api/projects/project-task', static fn(Request $request) => $api->projectTask($request));
    $router->post('/api/projects/project-task', static fn(Request $request) => $api->projectTask($request));
    $router->patch('/api/projects/project-task', static fn(Request $request) => $api->projectTask($request));
    $router->get('/api/projects/timesheet', static fn(Request $request) => $api->timesheet($request));
    $router->post('/api/projects/timesheet', static fn(Request $request) => $api->timesheet($request));
    $router->patch('/api/projects/timesheet', static fn(Request $request) => $api->timesheet($request));

    $dashboard = $container->make(ProjectsDashboardWebController::class);
    $crud = $container->make(ProjectsCrudWebController::class);
    $router->get('/projects', static fn(Request $request) => $dashboard->index($request));
    $router->get('/projects/dashboard', static fn(Request $request) => $dashboard->index($request));
    $router->get('/projects/project', static fn(Request $request) => $crud->index($request, 'project'));
    $router->get('/projects/project-task', static fn(Request $request) => $crud->index($request, 'project-task'));
    $router->get('/projects/timesheet', static fn(Request $request) => $crud->index($request, 'timesheet'));
};
