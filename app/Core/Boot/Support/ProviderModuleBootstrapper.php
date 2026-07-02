<?php

declare(strict_types=1);

namespace App\Core\Boot\Support;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Container\Contracts\BootableProviderInterface;
use App\Core\Container\Contracts\ServiceProviderInterface;
use App\Core\Module\Contracts\ModuleProvider;
use App\Core\Module\ModuleManifest;
use RuntimeException;

/**
 * Production bootstrapper that boots a module through its declared provider.
 *
 * Resolves the provider class from the service container and runs its register
 * + boot phases. Supports {@see ModuleProvider} (module-native) and the
 * container's own {@see ServiceProviderInterface}/{@see BootableProviderInterface}
 * so modules can be wired either way.
 */
final class ProviderModuleBootstrapper extends AbstractModuleBootstrapper
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function boot(ModuleManifest $manifest): void
    {
        $providerClass = $manifest->provider;

        if (! class_exists($providerClass)) {
            throw new RuntimeException(sprintf(
                'Module "%s" declares provider "%s" which does not exist.',
                $manifest->name,
                $providerClass,
            ));
        }

        $provider = $this->container->make($providerClass);

        if ($provider instanceof ModuleProvider) {
            $provider->register($this->container);
            $provider->boot($this->container);

            return;
        }

        if ($provider instanceof ServiceProviderInterface) {
            $this->container->registerProvider($provider);

            if ($provider instanceof BootableProviderInterface) {
                $provider->boot($this->container);
            }
        }
    }
}
