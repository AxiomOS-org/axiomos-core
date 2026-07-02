<?php

declare(strict_types=1);

namespace App\Core\Kernel\Events;

use App\Core\Boot\BootReport;

/**
 * Fired when the kernel has completed boot and is ready to serve traffic.
 */
final readonly class KernelReady
{
    public function __construct(public BootReport $report)
    {
    }
}
