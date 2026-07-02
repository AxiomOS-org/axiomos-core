<?php

declare(strict_types=1);

namespace Tests\Support\QA;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Tests\Support\Stability\KernelTestHarness;

/**
 * HTTP method and API contract probes.
 */
class RouteMatrixProbe extends KernelTestHarness
{
    /**
     * @return list<string>
     */
    public function probeAllRoutes(): array
    {
        $failures = [];

        foreach ($this->router()->getRoutes() as $route) {
            if (! $route instanceof Route) {
                continue;
            }

            $uri = $route->uri();

            if (str_contains($uri, '{')) {
                $uri = $this->substituteRouteUri($uri);
            }

            if (str_contains($uri, '{')) {
                continue;
            }

            $path = '/' . ltrim($uri, '/');

            foreach ($route->methods() as $method) {
                $method = strtoupper($method);

                if (in_array($method, ['HEAD', 'OPTIONS'], true)) {
                    continue;
                }

                $request = Request::create($path, $method, server: [
                    'HTTP_ACCEPT' => 'application/json',
                    'CONTENT_TYPE' => 'application/json',
                ]);

                if (in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
                    $request->initialize([], [], [], [], [], $request->server->all(), '{}');
                }

                $response = $this->kernel->handle($request);

                if ($response->getStatusCode() >= 500) {
                    $failures[] = sprintf('%s %s returned %d', $method, $path, $response->getStatusCode());
                }
            }
        }

        return $failures;
    }

    /**
     * @return array<string, mixed>
     */
    public function healthContract(): array
    {
        $content = $this->kernel->handle(Request::create('/health', 'GET'))->getContent();
        self::assertIsString($content);

        /** @var array<string, mixed> $payload */
        $payload = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        return $payload;
    }
}
