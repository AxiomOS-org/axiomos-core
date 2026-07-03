<?php
declare(strict_types=1);
use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Sales\Http\Controllers\Api\SalesApiController;
use Modules\Sales\Http\Controllers\Web\SalesCrudWebController;
use Modules\Sales\Http\Controllers\Web\SalesDashboardWebController;
return static function (Router $router, ContainerInterface $container): void {
    $api = $container->make(SalesApiController::class);
    $router->get('/api/sales/customer', static fn(Request $request) => $api->customer($request));
    $router->post('/api/sales/customer', static fn(Request $request) => $api->customer($request));
    $router->patch('/api/sales/customer', static fn(Request $request) => $api->customer($request));
    $router->get('/api/sales/sales-order', static fn(Request $request) => $api->salesOrder($request));
    $router->post('/api/sales/sales-order', static fn(Request $request) => $api->salesOrder($request));
    $router->patch('/api/sales/sales-order', static fn(Request $request) => $api->salesOrder($request));
    $router->get('/api/sales/sales-invoice', static fn(Request $request) => $api->salesInvoice($request));
    $router->post('/api/sales/sales-invoice', static fn(Request $request) => $api->salesInvoice($request));
    $router->patch('/api/sales/sales-invoice', static fn(Request $request) => $api->salesInvoice($request));
    $router->post('/api/sales/posting/submit', static fn(Request $request) => $api->postingSubmit($request));

    $dashboard = $container->make(SalesDashboardWebController::class);
    $crud = $container->make(SalesCrudWebController::class);
    $router->get('/sales', static fn(Request $request) => $dashboard->index($request));
    $router->get('/sales/dashboard', static fn(Request $request) => $dashboard->index($request));
    $router->get('/sales/customer', static fn(Request $request) => $crud->index($request, 'customer'));
    $router->get('/sales/sales-order', static fn(Request $request) => $crud->index($request, 'sales-order'));
    $router->get('/sales/sales-invoice', static fn(Request $request) => $crud->index($request, 'sales-invoice'));
};
