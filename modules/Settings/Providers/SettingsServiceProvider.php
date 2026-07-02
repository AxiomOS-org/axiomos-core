<?php

declare(strict_types=1);

namespace Modules\Settings\Providers;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;

/**
 * Service provider for the Settings module.
 *
 * Core tier (priority 1). Publishes its {@see ModuleInfo} marker and will host
 * platform-wide settings storage in a later sprint.
 */
final class SettingsServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.settings', new ModuleInfo('Settings', '1.0.0'));
    }
}
