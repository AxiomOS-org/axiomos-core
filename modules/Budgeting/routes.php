<?php
declare(strict_types=1);
use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Budgeting\Http\Controllers\Api\BudgetingApiController;
use Modules\Budgeting\Http\Controllers\Web\BudgetingCrudWebController;
use Modules\Budgeting\Http\Controllers\Web\BudgetingDashboardWebController;
return static function (Router $router, ContainerInterface $container): void {
    $api = $container->make(BudgetingApiController::class);
    $router->get('/api/budgeting/budget-version', static fn(Request $request) => $api->budgetVersion($request));
    $router->post('/api/budgeting/budget-version', static fn(Request $request) => $api->budgetVersion($request));
    $router->patch('/api/budgeting/budget-version', static fn(Request $request) => $api->budgetVersion($request));
    $router->get('/api/budgeting/budget-line', static fn(Request $request) => $api->budgetLine($request));
    $router->post('/api/budgeting/budget-line', static fn(Request $request) => $api->budgetLine($request));
    $router->patch('/api/budgeting/budget-line', static fn(Request $request) => $api->budgetLine($request));

    $dashboard = $container->make(BudgetingDashboardWebController::class);
    $crud = $container->make(BudgetingCrudWebController::class);
    $router->get('/budgeting', static fn(Request $request) => $dashboard->index($request));
    $router->get('/budgeting/dashboard', static fn(Request $request) => $dashboard->index($request));
    $router->get('/budgeting/budget-version', static fn(Request $request) => $crud->index($request, 'budget-version'));
    $router->get('/budgeting/budget-line', static fn(Request $request) => $crud->index($request, 'budget-line'));
};
