<?php
declare(strict_types=1);
use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Accounting\Http\Controllers\Api\AccountingApiController;
use Modules\Accounting\Http\Controllers\Web\AccountingCrudWebController;
use Modules\Accounting\Http\Controllers\Web\AccountingDashboardWebController;
return static function (Router $router, ContainerInterface $container): void {
    $api=$container->make(AccountingApiController::class);
    $router->get('/api/accounting/accounts', static fn(Request $request) => $api->accounts($request));
    $router->post('/api/accounting/accounts', static fn(Request $request) => $api->accounts($request));
    $router->get('/api/accounting/fiscal-years', static fn(Request $request) => $api->fiscalYears($request));
    $router->get('/api/accounting/periods', static fn(Request $request) => $api->periods($request));
    $router->get('/api/accounting/voucher-types', static fn(Request $request) => $api->voucherTypes($request));
    $router->get('/api/accounting/documents', static fn(Request $request) => $api->documents($request));
    $router->get('/api/accounting/journals', static fn(Request $request) => $api->journals($request));
    $router->post('/api/accounting/posting/submit', static fn(Request $request) => $api->postingSubmit($request));
    $router->post('/api/accounting/posting/reverse', static fn(Request $request) => $api->postingReverse($request));
    $router->post('/api/accounting/posting/preview', static fn(Request $request) => $api->postingPreview($request));
    $router->get('/api/accounting/dimensions/cost-centers', static fn(Request $request) => $api->costCenters($request));
    $router->get('/api/accounting/dimensions/profit-centers', static fn(Request $request) => $api->profitCenters($request));
    $router->get('/api/accounting/reports/trial-balance', static fn(Request $request) => $api->trialBalance($request));
    $router->get('/api/accounting/reports/balance-sheet', static fn(Request $request) => $api->balanceSheet($request));
    $router->get('/api/accounting/reports/profit-loss', static fn(Request $request) => $api->profitLoss($request));
    $router->get('/api/accounting/reports/cash-flow', static fn(Request $request) => $api->cashFlow($request));
    $router->get('/api/accounting/exchange-rates', static fn(Request $request) => $api->exchangeRates($request));

    $dashboard=$container->make(AccountingDashboardWebController::class);
    $crud=$container->make(AccountingCrudWebController::class);
    $router->get('/accounting', static fn(Request $request) => $dashboard->index($request));
    $router->get('/accounting/dashboard', static fn(Request $request) => $dashboard->index($request));
    $router->get('/accounting/accounts', static fn(Request $request) => $crud->index($request,'accounts'));
    $router->get('/accounting/documents', static fn(Request $request) => $crud->index($request,'documents'));
    $router->get('/accounting/journals', static fn(Request $request) => $crud->index($request,'journals'));
    $router->get('/accounting/fiscal-years', static fn(Request $request) => $crud->index($request,'fiscal-years'));
    $router->get('/accounting/periods', static fn(Request $request) => $crud->index($request,'periods'));
};

