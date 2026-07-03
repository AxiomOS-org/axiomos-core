<?php
declare(strict_types=1);
use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\FixedAssets\Http\Controllers\Api\FixedAssetsApiController;
use Modules\FixedAssets\Http\Controllers\Web\FixedAssetsCrudWebController;
use Modules\FixedAssets\Http\Controllers\Web\FixedAssetsDashboardWebController;
return static function (Router $router, ContainerInterface $container): void {
    $api = $container->make(FixedAssetsApiController::class);
    $router->get('/api/assets/fixed-asset', static fn(Request $request) => $api->fixedAsset($request));
    $router->post('/api/assets/fixed-asset', static fn(Request $request) => $api->fixedAsset($request));
    $router->patch('/api/assets/fixed-asset', static fn(Request $request) => $api->fixedAsset($request));
    $router->get('/api/assets/depreciation-run', static fn(Request $request) => $api->depreciationRun($request));
    $router->post('/api/assets/depreciation-run', static fn(Request $request) => $api->depreciationRun($request));
    $router->patch('/api/assets/depreciation-run', static fn(Request $request) => $api->depreciationRun($request));
    $router->post('/api/assets/posting/submit', static fn(Request $request) => $api->postingSubmit($request));

    $dashboard = $container->make(FixedAssetsDashboardWebController::class);
    $crud = $container->make(FixedAssetsCrudWebController::class);
    $router->get('/assets', static fn(Request $request) => $dashboard->index($request));
    $router->get('/assets/dashboard', static fn(Request $request) => $dashboard->index($request));
    $router->get('/assets/fixed-asset', static fn(Request $request) => $crud->index($request, 'fixed-asset'));
    $router->get('/assets/depreciation-run', static fn(Request $request) => $crud->index($request, 'depreciation-run'));
};
