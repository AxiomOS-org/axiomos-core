<?php
declare(strict_types=1);
use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\POS\Http\Controllers\Api\POSApiController;
use Modules\POS\Http\Controllers\Web\POSCrudWebController;
use Modules\POS\Http\Controllers\Web\POSDashboardWebController;
return static function (Router $router, ContainerInterface $container): void {
    $api = $container->make(POSApiController::class);
    $router->get('/api/pos/pos-terminal', static fn(Request $request) => $api->posTerminal($request));
    $router->post('/api/pos/pos-terminal', static fn(Request $request) => $api->posTerminal($request));
    $router->patch('/api/pos/pos-terminal', static fn(Request $request) => $api->posTerminal($request));
    $router->get('/api/pos/pos-session', static fn(Request $request) => $api->posSession($request));
    $router->post('/api/pos/pos-session', static fn(Request $request) => $api->posSession($request));
    $router->patch('/api/pos/pos-session', static fn(Request $request) => $api->posSession($request));
    $router->get('/api/pos/pos-order', static fn(Request $request) => $api->posOrder($request));
    $router->post('/api/pos/pos-order', static fn(Request $request) => $api->posOrder($request));
    $router->patch('/api/pos/pos-order', static fn(Request $request) => $api->posOrder($request));
    $router->post('/api/pos/posting/submit', static fn(Request $request) => $api->postingSubmit($request));

    $dashboard = $container->make(POSDashboardWebController::class);
    $crud = $container->make(POSCrudWebController::class);
    $router->get('/pos', static fn(Request $request) => $dashboard->index($request));
    $router->get('/pos/dashboard', static fn(Request $request) => $dashboard->index($request));
    $router->get('/pos/pos-terminal', static fn(Request $request) => $crud->index($request, 'pos-terminal'));
    $router->get('/pos/pos-session', static fn(Request $request) => $crud->index($request, 'pos-session'));
    $router->get('/pos/pos-order', static fn(Request $request) => $crud->index($request, 'pos-order'));
};
