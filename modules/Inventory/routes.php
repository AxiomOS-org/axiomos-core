<?php
declare(strict_types=1);
use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Inventory\Http\Controllers\Api\InventoryApiController;
use Modules\Inventory\Http\Controllers\Web\InventoryCrudWebController;
use Modules\Inventory\Http\Controllers\Web\InventoryDashboardWebController;
return static function (Router $router, ContainerInterface $container): void {
    $api = $container->make(InventoryApiController::class);
    $router->get('/api/inventory/warehouse', static fn(Request $request) => $api->warehouse($request));
    $router->post('/api/inventory/warehouse', static fn(Request $request) => $api->warehouse($request));
    $router->patch('/api/inventory/warehouse', static fn(Request $request) => $api->warehouse($request));
    $router->get('/api/inventory/item', static fn(Request $request) => $api->item($request));
    $router->post('/api/inventory/item', static fn(Request $request) => $api->item($request));
    $router->patch('/api/inventory/item', static fn(Request $request) => $api->item($request));
    $router->get('/api/inventory/stock-movement', static fn(Request $request) => $api->stockMovement($request));
    $router->post('/api/inventory/stock-movement', static fn(Request $request) => $api->stockMovement($request));
    $router->patch('/api/inventory/stock-movement', static fn(Request $request) => $api->stockMovement($request));

    $dashboard = $container->make(InventoryDashboardWebController::class);
    $crud = $container->make(InventoryCrudWebController::class);
    $router->get('/inventory', static fn(Request $request) => $dashboard->index($request));
    $router->get('/inventory/dashboard', static fn(Request $request) => $dashboard->index($request));
    $router->get('/inventory/warehouse', static fn(Request $request) => $crud->index($request, 'warehouse'));
    $router->get('/inventory/item', static fn(Request $request) => $crud->index($request, 'item'));
    $router->get('/inventory/stock-movement', static fn(Request $request) => $crud->index($request, 'stock-movement'));
};
