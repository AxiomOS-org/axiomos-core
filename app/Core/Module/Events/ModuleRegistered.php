<?php

declare(strict_types=1);

namespace App\Core\Module\Events;

use App\Core\Module\ModuleManifest;

/**
 * Fired when a manifest is admitted into the registry for the first time.
 */
final readonly class ModuleRegistered
{
    public function __construct(public ModuleManifest $manifest)
    {
    }
}
