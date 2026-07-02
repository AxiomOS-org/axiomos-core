<?php

declare(strict_types=1);

namespace Tests\Stability;

use Tests\Support\Stability\KernelTestHarness;
use Tests\Support\Stability\RouteCatalog;

final class ContainerBindingTest extends KernelTestHarness
{
    public function test_singleton_services_resolve_from_container(): void
    {
        $failures = [];

        foreach (RouteCatalog::resolvableSingletons() as $class) {
            try {
                $instance = $this->container()->make($class);
                self::assertInstanceOf($class, $instance);
            } catch (\Throwable $exception) {
                $failures[] = $class . ': ' . $exception->getMessage();
            }
        }

        self::assertSame([], $failures, "Missing container bindings:\n" . implode("\n", $failures));
    }
}
