<?php
declare(strict_types=1);
use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\CRM\Http\Controllers\Api\CRMApiController;
use Modules\CRM\Http\Controllers\Web\CRMCrudWebController;
use Modules\CRM\Http\Controllers\Web\CRMDashboardWebController;
return static function (Router $router, ContainerInterface $container): void {
    $api = $container->make(CRMApiController::class);
    $router->get('/api/crm/lead', static fn(Request $request) => $api->lead($request));
    $router->post('/api/crm/lead', static fn(Request $request) => $api->lead($request));
    $router->patch('/api/crm/lead', static fn(Request $request) => $api->lead($request));
    $router->get('/api/crm/opportunity', static fn(Request $request) => $api->opportunity($request));
    $router->post('/api/crm/opportunity', static fn(Request $request) => $api->opportunity($request));
    $router->patch('/api/crm/opportunity', static fn(Request $request) => $api->opportunity($request));
    $router->get('/api/crm/crm-activity', static fn(Request $request) => $api->crmActivity($request));
    $router->post('/api/crm/crm-activity', static fn(Request $request) => $api->crmActivity($request));
    $router->patch('/api/crm/crm-activity', static fn(Request $request) => $api->crmActivity($request));

    $dashboard = $container->make(CRMDashboardWebController::class);
    $crud = $container->make(CRMCrudWebController::class);
    $router->get('/crm', static fn(Request $request) => $dashboard->index($request));
    $router->get('/crm/dashboard', static fn(Request $request) => $dashboard->index($request));
    $router->get('/crm/lead', static fn(Request $request) => $crud->index($request, 'lead'));
    $router->get('/crm/opportunity', static fn(Request $request) => $crud->index($request, 'opportunity'));
    $router->get('/crm/crm-activity', static fn(Request $request) => $crud->index($request, 'crm-activity'));
};
