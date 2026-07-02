<?php

declare(strict_types=1);

namespace App\Core\Module\Events;

use App\Core\Module\ModuleManifest;

/**
 * Fired once a discovered manifest has passed every validation rule and has
 * been hydrated into a trustworthy {@see ModuleManifest}.
 */
final readonly class ModuleLoaded
{
    public function __construct(public ModuleManifest $manifest)
    {
    }
}
