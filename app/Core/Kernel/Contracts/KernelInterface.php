<?php

declare(strict_types=1);

namespace App\Core\Kernel\Contracts;

use App\Core\Boot\BootReport;
use App\Core\Configuration\Contracts\ConfigurationManager;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Kernel\KernelState;
use App\Core\Module\ModuleRegistry;

/**
 * Kernel contract consumed by {@see \App\Core\Kernel\KernelManager}.
 *
 * The manager depends on this interface — never on the concrete {@see Kernel}
 * class — so the kernel can be substituted, decorated, or mocked without
 * changing the public manager API.
 */
interface KernelInterface
{
    public function initialize(): void;

    public function register(): void;

    public function boot(): BootReport;

    public function ready(BootReport $report): void;

    public function shutdown(): void;

    public function reload(): BootReport;

    /**
     * @return array<string, mixed>
     */
    public function status(): array;

    /**
     * @return array<string, mixed>
     */
    public function health(): array;

    /**
     * @return array<string, mixed>
     */
    public function metrics(): array;

    public function state(): KernelState;

    public function container(): ContainerInterface;

    public function registry(): ModuleRegistry;

    public function lastBootReport(): ?BootReport;
}
