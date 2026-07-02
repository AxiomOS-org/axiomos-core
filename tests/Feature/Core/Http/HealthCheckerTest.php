<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Http;

use App\Core\Http\Health\HealthCheckInterface;
use App\Core\Http\Health\HealthChecker;
use App\Core\Http\Health\HealthResult;
use App\Core\Http\Health\HealthStatus;
use PHPUnit\Framework\TestCase;

final class HealthCheckerTest extends TestCase
{
    public function test_all_ok_reports_healthy_200(): void
    {
        $checker = new HealthChecker(
            $this->check('a', HealthStatus::Ok),
            $this->check('b', HealthStatus::Ok),
        );

        $report = $checker->run();

        self::assertTrue($report->isHealthy());
        self::assertSame(HealthStatus::Ok, $report->status);
        self::assertSame(200, $report->httpStatus());
        self::assertCount(2, $report->results);
    }

    public function test_degraded_is_surfaced_but_still_200(): void
    {
        $checker = new HealthChecker(
            $this->check('a', HealthStatus::Ok),
            $this->check('b', HealthStatus::Degraded),
        );

        $report = $checker->run();

        self::assertFalse($report->isHealthy());
        self::assertSame(HealthStatus::Degraded, $report->status);
        self::assertSame(200, $report->httpStatus());
    }

    public function test_down_returns_503(): void
    {
        $checker = new HealthChecker(
            $this->check('a', HealthStatus::Ok),
            $this->check('b', HealthStatus::Degraded),
            $this->check('c', HealthStatus::Down),
        );

        $report = $checker->run();

        self::assertSame(HealthStatus::Down, $report->status);
        self::assertSame(503, $report->httpStatus());
    }

    public function test_registered_checks_run_too(): void
    {
        $checker = new HealthChecker($this->check('a', HealthStatus::Ok));
        $checker->register($this->check('b', HealthStatus::Down));

        $report = $checker->run();

        self::assertSame(HealthStatus::Down, $report->status);
        self::assertCount(2, $report->results);
    }

    private function check(string $name, HealthStatus $status): HealthCheckInterface
    {
        return new class($name, $status) implements HealthCheckInterface {
            public function __construct(
                private readonly string $checkName,
                private readonly HealthStatus $status,
            ) {
            }

            public function name(): string
            {
                return $this->checkName;
            }

            public function run(): HealthResult
            {
                return new HealthResult($this->checkName, $this->status);
            }
        };
    }
}
