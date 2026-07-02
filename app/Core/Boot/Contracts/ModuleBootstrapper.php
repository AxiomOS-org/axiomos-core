<?php

declare(strict_types=1);

namespace App\Core\Boot\Contracts;

use App\Core\Module\ModuleManifest;

/**
 * Full module lifecycle contract.
 *
 * Only {@see boot()} is invoked by the boot manager today. The remaining phases
 * are defined now so marketplace modules, hot-reload and graceful shutdown can
 * adopt them without a breaking interface change in a later sprint.
 */
interface ModuleBootstrapper
{
    public function initialize(ModuleManifest $manifest): void;

    public function register(ModuleManifest $manifest): void;

    public function boot(ModuleManifest $manifest): void;

    public function ready(ModuleManifest $manifest): void;

    public function shutdown(ModuleManifest $manifest): void;
}
