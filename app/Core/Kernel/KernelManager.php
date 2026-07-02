<?php

declare(strict_types=1);

namespace App\Core\Kernel;

use App\Core\Boot\BootReport;
use App\Core\Kernel\Contracts\KernelInterface;

/**
 * Public entry point for the AxiomOS kernel.
 *
 * Depends on {@see KernelInterface} — never the concrete {@see Kernel} class —
 * so the kernel can be substituted, decorated, or mocked without changing this
 * manager's public API.
 */
final class KernelManager
{
    public function __construct(private readonly KernelInterface $kernel)
    {
    }

    public function boot(): BootReport
    {
        return $this->kernel->boot();
    }

    public function shutdown(): void
    {
        $this->kernel->shutdown();
    }

    public function reload(): BootReport
    {
        return $this->kernel->reload();
    }

    /**
     * @return array<string, mixed>
     */
    public function status(): array
    {
        return $this->kernel->status();
    }

    /**
     * @return array<string, mixed>
     */
    public function health(): array
    {
        return $this->kernel->health();
    }

    /**
     * @return array<string, mixed>
     */
    public function metrics(): array
    {
        return $this->kernel->metrics();
    }

    public function kernel(): KernelInterface
    {
        return $this->kernel;
    }
}
