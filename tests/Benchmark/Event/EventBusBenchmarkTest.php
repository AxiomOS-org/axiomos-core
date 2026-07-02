<?php

declare(strict_types=1);

namespace Tests\Benchmark\Event;

use App\Core\Event\EventBus;
use App\Core\Event\EventBusBuilder;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('benchmark')]
final class EventBusBenchmarkTest extends TestCase
{
    private const ITERATIONS = 10_000;

    public function test_benchmark_single_listener_dispatch(): void
    {
        $bus = (new EventBusBuilder())->build();
        $bus->listen(BenchmarkEvent::class, static fn () => null);

        $start = hrtime(true);

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $bus->dispatch(new BenchmarkEvent());
        }

        $elapsedMs = (hrtime(true) - $start) / 1_000_000;

        self::assertLessThan(1_500.0, $elapsedMs, sprintf(
            'Dispatch of %d events took %.2f ms (budget: 1500 ms).',
            self::ITERATIONS,
            $elapsedMs,
        ));
    }

    public function test_benchmark_wildcard_dispatch(): void
    {
        $bus = (new EventBusBuilder())->build();

        for ($i = 0; $i < 10; $i++) {
            $bus->listen('Tests\\Benchmark\\Event\\*', static fn () => null);
        }

        $start = hrtime(true);

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $bus->dispatch(new BenchmarkEvent());
        }

        $elapsedMs = (hrtime(true) - $start) / 1_000_000;

        self::assertLessThan(3_000.0, $elapsedMs, sprintf(
            'Wildcard dispatch of %d events took %.2f ms (budget: 3000 ms).',
            self::ITERATIONS,
            $elapsedMs,
        ));
    }
}

final class BenchmarkEvent
{
}
