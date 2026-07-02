<?php

declare(strict_types=1);

namespace Modules\Automation\Providers;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Platform\Automation\AutomationSdk;

final class AutomationServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.automation', new ModuleInfo('Automation', '1.0.0'));
    }

    public function boot(ContainerInterface $container): void
    {
        if ($container->has(AutomationSdk::class)) {
            $container->instance('platform.automation.sdk', $container->make(AutomationSdk::class));
        }
    }
}
