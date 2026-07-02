<?php

declare(strict_types=1);

namespace App\Core\Module\Events;

use App\Core\Module\ModuleManifest;

/**
 * Fired when a registered module transitions from disabled to enabled.
 */
final readonly class ModuleEnabled
{
    public function __construct(public ModuleManifest $manifest)
    {
    }
}
