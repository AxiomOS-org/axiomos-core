<?php

declare(strict_types=1);

namespace App\Core\Http\Health\Checks;

use App\Core\Http\Health\HealthCheckInterface;
use App\Core\Http\Health\HealthResult;
use App\Core\Http\Health\HealthStatus;
use App\Core\Kernel\Contracts\KernelInterface;
use App\Core\Kernel\KernelState;

/**
 * Reports the kernel as healthy only when it has reached the Ready state.
 */
final class KernelReadyCheck implements HealthCheckInterface
{
    public function __construct(private readonly KernelInterface $kernel)
    {
    }

    public function name(): string
    {
        return 'kernel';
    }

    public function run(): HealthResult
    {
        $state = $this->kernel->state();
        $ready = $state === KernelState::Ready;

        return new HealthResult(
            name: $this->name(),
            status: $ready ? HealthStatus::Ok : HealthStatus::Down,
            message: $ready ? 'Kernel is ready.' : sprintf('Kernel is in "%s" state.', $state->value),
            data: ['state' => $state->value],
        );
    }
}
