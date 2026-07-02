<?php

declare(strict_types=1);

namespace App\Core\Boot\Events;

use App\Core\Module\ModuleManifest;

/**
 * Fired immediately before a single enabled module is booted.
 */
final readonly class ModuleBooting
{
    public function __construct(public ModuleManifest $manifest)
    {
    }
}
