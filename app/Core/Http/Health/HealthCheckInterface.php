<?php

declare(strict_types=1);

namespace App\Core\Http\Health;

/**
 * A single, independently runnable health probe.
 */
interface HealthCheckInterface
{
    public function name(): string;

    public function run(): HealthResult;
}
