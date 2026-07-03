<?php
declare(strict_types=1);
use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\HR\Http\Controllers\Api\HRApiController;
use Modules\HR\Http\Controllers\Web\HRCrudWebController;
use Modules\HR\Http\Controllers\Web\HRDashboardWebController;
return static function (Router $router, ContainerInterface $container): void {
    $api = $container->make(HRApiController::class);
    $router->get('/api/hr/employee', static fn(Request $request) => $api->employee($request));
    $router->post('/api/hr/employee', static fn(Request $request) => $api->employee($request));
    $router->patch('/api/hr/employee', static fn(Request $request) => $api->employee($request));
    $router->get('/api/hr/attendance-record', static fn(Request $request) => $api->attendanceRecord($request));
    $router->post('/api/hr/attendance-record', static fn(Request $request) => $api->attendanceRecord($request));
    $router->patch('/api/hr/attendance-record', static fn(Request $request) => $api->attendanceRecord($request));
    $router->get('/api/hr/payroll-run', static fn(Request $request) => $api->payrollRun($request));
    $router->post('/api/hr/payroll-run', static fn(Request $request) => $api->payrollRun($request));
    $router->patch('/api/hr/payroll-run', static fn(Request $request) => $api->payrollRun($request));
    $router->post('/api/hr/posting/submit', static fn(Request $request) => $api->postingSubmit($request));

    $dashboard = $container->make(HRDashboardWebController::class);
    $crud = $container->make(HRCrudWebController::class);
    $router->get('/hr', static fn(Request $request) => $dashboard->index($request));
    $router->get('/hr/dashboard', static fn(Request $request) => $dashboard->index($request));
    $router->get('/hr/employee', static fn(Request $request) => $crud->index($request, 'employee'));
    $router->get('/hr/attendance-record', static fn(Request $request) => $crud->index($request, 'attendance-record'));
    $router->get('/hr/payroll-run', static fn(Request $request) => $crud->index($request, 'payroll-run'));
};
