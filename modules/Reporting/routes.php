<?php
declare(strict_types=1);
use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Reporting\Http\Controllers\Api\ReportingApiController;
use Modules\Reporting\Http\Controllers\Web\ReportingCrudWebController;
use Modules\Reporting\Http\Controllers\Web\ReportingDashboardWebController;
return static function (Router $router, ContainerInterface $container): void {
    $api = $container->make(ReportingApiController::class);
    $router->get('/api/reporting/report-definition', static fn(Request $request) => $api->reportDefinition($request));
    $router->post('/api/reporting/report-definition', static fn(Request $request) => $api->reportDefinition($request));
    $router->patch('/api/reporting/report-definition', static fn(Request $request) => $api->reportDefinition($request));
    $router->get('/api/reporting/report-snapshot', static fn(Request $request) => $api->reportSnapshot($request));
    $router->post('/api/reporting/report-snapshot', static fn(Request $request) => $api->reportSnapshot($request));
    $router->patch('/api/reporting/report-snapshot', static fn(Request $request) => $api->reportSnapshot($request));
    $router->get('/api/reporting/dashboard', static fn(Request $request) => $api->dashboard($request));

    $dashboard = $container->make(ReportingDashboardWebController::class);
    $crud = $container->make(ReportingCrudWebController::class);
    $router->get('/reporting', static fn(Request $request) => $dashboard->index($request));
    $router->get('/reporting/dashboard', static fn(Request $request) => $dashboard->index($request));
    $router->get('/reporting/report-definition', static fn(Request $request) => $crud->index($request, 'report-definition'));
    $router->get('/reporting/report-snapshot', static fn(Request $request) => $crud->index($request, 'report-snapshot'));
};
