<?php

declare(strict_types=1);

namespace App\Core\Module\Events;

use Throwable;

/**
 * Fired when a declared module fails validation, immediately before the
 * discovery pipeline rethrows. Lets the marketplace and operators record which
 * module broke the boot sequence and why, without swallowing the failure.
 */
final readonly class ModuleFailed
{
    public function __construct(
        public string $manifestPath,
        public Throwable $exception,
    ) {
    }
}
