<?php

declare(strict_types=1);

namespace App\Core\Module\Contracts;

use App\Core\Container\Contracts\ContainerInterface;

/**
 * Lifecycle contract every AxiomOS module provider implements.
 *
 * A module's `module.json` declares a provider class; the boot manager resolves
 * it and runs {@see register()} (bind services) then {@see boot()} (wire behaviour
 * once all core services exist).
 */
interface ModuleProvider
{
    public function register(ContainerInterface $container): void;

    public function boot(ContainerInterface $container): void;
}
