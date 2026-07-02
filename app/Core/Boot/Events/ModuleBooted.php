<?php

declare(strict_types=1);

namespace App\Core\Boot\Events;

use App\Core\Module\ModuleManifest;

/**
 * Fired after a single enabled module has booted successfully.
 */
final readonly class ModuleBooted
{
    public function __construct(public ModuleManifest $manifest)
    {
    }
}
