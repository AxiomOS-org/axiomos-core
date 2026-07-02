<?php

declare(strict_types=1);

namespace Modules\Plugin\Providers;

use App\ADT\Extension\ExtensionRegistry;
use App\ADT\Extension\PluginSdk;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;

final class PluginServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.plugin', new ModuleInfo('Plugin', '1.0.0'));
    }

    public function boot(ContainerInterface $container): void
    {
        if (! $container->has(PluginSdk::class)) {
            return;
        }

        $sdk = $container->make(PluginSdk::class);
        $registry = $container->make(ExtensionRegistry::class);
        $container->instance('platform.plugin.sdk', $sdk);
        $container->instance('platform.extension.registry', $registry);
    }
}
