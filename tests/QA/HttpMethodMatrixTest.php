<?php

declare(strict_types=1);

namespace Tests\QA;

use Tests\Support\QA\RouteMatrixProbe;

final class HttpMethodMatrixTest extends RouteMatrixProbe
{
    public function test_all_registered_routes_avoid_500_across_methods(): void
    {
        $failures = $this->probeAllRoutes();

        self::assertSame([], $failures, implode("\n", $failures));
    }
}
