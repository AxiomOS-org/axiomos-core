<?php

declare(strict_types=1);

namespace App\Core\Http\Health;

/**
 * Runs every registered health check and aggregates the worst status.
 */
final class HealthChecker
{
    /** @var list<HealthCheckInterface> */
    private array $checks;

    public function __construct(HealthCheckInterface ...$checks)
    {
        $this->checks = array_values($checks);
    }

    public function register(HealthCheckInterface $check): void
    {
        $this->checks[] = $check;
    }

    public function run(): HealthReport
    {
        $results = [];
        $status = HealthStatus::Ok;

        foreach ($this->checks as $check) {
            $result = $check->run();
            $results[] = $result;
            $status = $status->worst($result->status);
        }

        return new HealthReport($status, $results);
    }
}
