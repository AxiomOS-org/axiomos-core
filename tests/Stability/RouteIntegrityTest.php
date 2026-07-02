<?php

declare(strict_types=1);

namespace Tests\Stability;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Tests\Support\Stability\KernelTestHarness;

final class RouteIntegrityTest extends KernelTestHarness
{
    public function test_registered_routes_are_present(): void
    {
        self::assertGreaterThan(20, count($this->router()->getRoutes()));
    }

    public function test_unknown_route_returns_404_not_500(): void
    {
        $response = $this->kernel->handle(Request::create('/this-route-does-not-exist-' . bin2hex(random_bytes(4)), 'GET'));

        self::assertSame(404, $response->getStatusCode());
    }

    public function test_get_routes_do_not_return_500(): void
    {
        $failures = [];

        foreach ($this->router()->getRoutes() as $route) {
            if (! $route instanceof Route) {
                continue;
            }

            $methods = array_map('strtoupper', $route->methods());

            if (! in_array('GET', $methods, true)) {
                continue;
            }

            $uri = $route->uri();

            if (str_contains($uri, '{')) {
                $uri = $this->substituteRouteUri($uri);
            }

            if (str_contains($uri, '{')) {
                continue;
            }

            $response = $this->kernel->handle(Request::create('/' . ltrim($uri, '/'), 'GET'));
            $status = $response->getStatusCode();

            if ($status >= 500) {
                $failures[] = sprintf('GET /%s returned %d', $uri, $status);
            }
        }

        self::assertSame([], $failures, "Broken GET routes:\n" . implode("\n", $failures));
    }
}
