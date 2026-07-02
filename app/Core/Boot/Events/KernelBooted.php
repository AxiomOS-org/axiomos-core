<?php

declare(strict_types=1);

namespace App\Core\Boot\Events;

use App\Core\Boot\BootReport;

/**
 * Fired when the kernel boot sequence completes, carrying the final report.
 */
final readonly class KernelBooted
{
    public function __construct(public BootReport $report)
    {
    }
}
