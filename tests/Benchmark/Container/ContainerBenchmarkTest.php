<?php

declare(strict_types=1);

namespace Tests\Benchmark\Container;

use App\Core\Container\Container;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Performance benchmarks for container resolution hot paths.
 */
#[Group('benchmark')]
final class ContainerBenchmarkTest extends TestCase
{
    private const ITERATIONS = 5_000;

    public function test_benchmark_singleton_resolution(): void
    {
        $container = new Container();
        $container->singleton(BenchmarkService::class, BenchmarkService::class);
        $container->make(BenchmarkService::class);

        $start = hrtime(true);

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $container->make(BenchmarkService::class);
        }

        $elapsedMs = (hrtime(true) - $start) / 1_000_000;

        self::assertLessThan(500.0, $elapsedMs, sprintf(
            'Singleton resolution for %d iterations took %.2f ms (budget: 500 ms).',
            self::ITERATIONS,
            $elapsedMs,
        ));
    }

    public function test_benchmark_auto_wiring_resolution(): void
    {
        $container = new Container();
        $container->singleton(BenchmarkDependency::class, BenchmarkDependency::class);

        $start = hrtime(true);

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $container->make(BenchmarkConsumer::class);
        }

        $elapsedMs = (hrtime(true) - $start) / 1_000_000;

        self::assertLessThan(2_000.0, $elapsedMs, sprintf(
            'Auto-wired resolution for %d iterations took %.2f ms (budget: 2000 ms).',
            self::ITERATIONS,
            $elapsedMs,
        ));
    }
}

final class BenchmarkDependency
{
}

final class BenchmarkService
{
}

final class BenchmarkConsumer
{
    public function __construct(public readonly BenchmarkDependency $dependency)
    {
    }
}
