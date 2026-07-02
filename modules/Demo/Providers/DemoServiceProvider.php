<?php

declare(strict_types=1);

namespace Modules\Demo\Providers;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;

/**
 * Service provider for the Demo module.
 */
final class DemoServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.demo', new ModuleInfo('Demo', '1.0.0'));
    }
}
