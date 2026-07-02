<?php

declare(strict_types=1);

namespace App\Core\Module\Events;

use App\Core\Module\ModuleManifest;

/**
 * Fired when a registered module transitions from enabled to disabled.
 */
final readonly class ModuleDisabled
{
    public function __construct(public ModuleManifest $manifest)
    {
    }
}
