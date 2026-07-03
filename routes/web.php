<?php

declare(strict_types=1);

use App\Core\Http\Controllers\HealthController;
use App\Core\Http\Controllers\HomeController;
use App\Core\Http\Controllers\MetricsController;
use App\Platform\Http\Controllers\PlatformPluginsController;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

/**
 * AxiomOS HTTP routes.
 *
 * Returns a registrar closure so the routing table stays declarative while the
 * controllers are constructed and injected by {@see \App\Core\Http\HttpKernelFactory}.
 */
return static function (
    Router $router,
    HomeController $home,
    HealthController $health,
    MetricsController $metrics,
    PlatformPluginsController $platformPlugins,
): void {
    $router->get('/', static fn (Request $request) => $home($request));
    $router->get('/health', static fn (Request $request) => $health($request));
    $router->get('/metrics', static fn (Request $request) => $metrics($request));

    $router->get('/api/platform/plugins', static fn (Request $request) => $platformPlugins->index($request));
    $router->get('/api/platform/plugins/{id}', static fn (Request $request, string $id) => $platformPlugins->show($request, $id));
};
