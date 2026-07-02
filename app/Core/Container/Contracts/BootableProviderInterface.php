<?php

declare(strict_types=1);

namespace App\Core\Container\Contracts;

/**
 * Service providers that require a boot phase after all registrations complete.
 */
interface BootableProviderInterface
{
    public function boot(ContainerInterface $container): void;
}
