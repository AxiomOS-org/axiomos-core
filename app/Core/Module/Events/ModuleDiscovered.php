<?php

declare(strict_types=1);

namespace App\Core\Module\Events;

/**
 * Fired when a `module.json` is located on disk and successfully decoded,
 * before validation runs. Consumed by tooling and (future) marketplace
 * telemetry that needs to observe raw discovery.
 */
final readonly class ModuleDiscovered
{
    public function __construct(public string $manifestPath)
    {
    }
}
