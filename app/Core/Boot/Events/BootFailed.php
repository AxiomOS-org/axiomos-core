<?php

declare(strict_types=1);

namespace App\Core\Boot\Events;

use App\Core\Module\ModuleManifest;
use Throwable;

/**
 * Fired when module discovery or an individual module boot fails.
 *
 * A null manifest indicates a discovery-level failure that prevented the
 * module from being identified.
 */
final readonly class BootFailed
{
    public function __construct(
        public ?ModuleManifest $manifest,
        public Throwable $exception,
    ) {
    }
}
