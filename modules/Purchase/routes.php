<?php
declare(strict_types=1);
use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Purchase\Http\Controllers\Api\PurchaseApiController;
use Modules\Purchase\Http\Controllers\Web\PurchaseCrudWebController;
use Modules\Purchase\Http\Controllers\Web\PurchaseDashboardWebController;
return static function (Router $router, ContainerInterface $container): void {
    $api = $container->make(PurchaseApiController::class);
    $router->get('/api/purchase/vendor', static fn(Request $request) => $api->vendor($request));
    $router->post('/api/purchase/vendor', static fn(Request $request) => $api->vendor($request));
    $router->patch('/api/purchase/vendor', static fn(Request $request) => $api->vendor($request));
    $router->get('/api/purchase/purchase-order', static fn(Request $request) => $api->purchaseOrder($request));
    $router->post('/api/purchase/purchase-order', static fn(Request $request) => $api->purchaseOrder($request));
    $router->patch('/api/purchase/purchase-order', static fn(Request $request) => $api->purchaseOrder($request));
    $router->get('/api/purchase/purchase-bill', static fn(Request $request) => $api->purchaseBill($request));
    $router->post('/api/purchase/purchase-bill', static fn(Request $request) => $api->purchaseBill($request));
    $router->patch('/api/purchase/purchase-bill', static fn(Request $request) => $api->purchaseBill($request));
    $router->post('/api/purchase/posting/submit', static fn(Request $request) => $api->postingSubmit($request));

    $dashboard = $container->make(PurchaseDashboardWebController::class);
    $crud = $container->make(PurchaseCrudWebController::class);
    $router->get('/purchase', static fn(Request $request) => $dashboard->index($request));
    $router->get('/purchase/dashboard', static fn(Request $request) => $dashboard->index($request));
    $router->get('/purchase/vendor', static fn(Request $request) => $crud->index($request, 'vendor'));
    $router->get('/purchase/purchase-order', static fn(Request $request) => $crud->index($request, 'purchase-order'));
    $router->get('/purchase/purchase-bill', static fn(Request $request) => $crud->index($request, 'purchase-bill'));
};
