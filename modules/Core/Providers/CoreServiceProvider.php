<?php

declare(strict_types=1);

namespace Modules\Core\Providers;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;

/**
 * Service provider for the Core module.
 *
 * The Core module is the platform's foundation tier (priority 1). It publishes a
 * {@see ModuleInfo} marker so downstream modules can confirm the core is active.
 */
final class CoreServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.core', new ModuleInfo('Core', '1.0.0'));
    }
}
