<?php
declare(strict_types=1);
use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Manufacturing\Http\Controllers\Api\ManufacturingApiController;
use Modules\Manufacturing\Http\Controllers\Web\ManufacturingCrudWebController;
use Modules\Manufacturing\Http\Controllers\Web\ManufacturingDashboardWebController;
return static function (Router $router, ContainerInterface $container): void {
    $api = $container->make(ManufacturingApiController::class);
    $router->get('/api/manufacturing/bill-of-material', static fn(Request $request) => $api->billOfMaterial($request));
    $router->post('/api/manufacturing/bill-of-material', static fn(Request $request) => $api->billOfMaterial($request));
    $router->patch('/api/manufacturing/bill-of-material', static fn(Request $request) => $api->billOfMaterial($request));
    $router->get('/api/manufacturing/work-order', static fn(Request $request) => $api->workOrder($request));
    $router->post('/api/manufacturing/work-order', static fn(Request $request) => $api->workOrder($request));
    $router->patch('/api/manufacturing/work-order', static fn(Request $request) => $api->workOrder($request));
    $router->get('/api/manufacturing/production-run', static fn(Request $request) => $api->productionRun($request));
    $router->post('/api/manufacturing/production-run', static fn(Request $request) => $api->productionRun($request));
    $router->patch('/api/manufacturing/production-run', static fn(Request $request) => $api->productionRun($request));
    $router->post('/api/manufacturing/posting/submit', static fn(Request $request) => $api->postingSubmit($request));

    $dashboard = $container->make(ManufacturingDashboardWebController::class);
    $crud = $container->make(ManufacturingCrudWebController::class);
    $router->get('/manufacturing', static fn(Request $request) => $dashboard->index($request));
    $router->get('/manufacturing/dashboard', static fn(Request $request) => $dashboard->index($request));
    $router->get('/manufacturing/bill-of-material', static fn(Request $request) => $crud->index($request, 'bill-of-material'));
    $router->get('/manufacturing/work-order', static fn(Request $request) => $crud->index($request, 'work-order'));
    $router->get('/manufacturing/production-run', static fn(Request $request) => $crud->index($request, 'production-run'));
};
