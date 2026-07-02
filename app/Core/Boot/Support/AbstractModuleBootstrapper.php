<?php

declare(strict_types=1);

namespace App\Core\Boot\Support;

use App\Core\Boot\Contracts\ModuleBootstrapper;
use App\Core\Module\ModuleManifest;

/**
 * Default module lifecycle implementation.
 *
 * Subclasses override {@see boot()} (and any future phase) while the unused
 * phases remain safe no-ops until the boot manager starts calling them.
 */
abstract class AbstractModuleBootstrapper implements ModuleBootstrapper
{
    public function initialize(ModuleManifest $manifest): void
    {
    }

    public function register(ModuleManifest $manifest): void
    {
    }

    public function shutdown(ModuleManifest $manifest): void
    {
    }

    public function ready(ModuleManifest $manifest): void
    {
    }
}
