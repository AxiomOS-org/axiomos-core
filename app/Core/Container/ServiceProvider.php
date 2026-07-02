<?php

declare(strict_types=1);

namespace App\Core\Container;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Container\Contracts\ServiceProviderInterface;

/**
 * Base service provider with sensible defaults for register/boot phases.
 */
abstract class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @return list<string>
     */
    public function provides(): array
    {
        return [];
    }

    abstract public function register(ContainerInterface $container): void;
}
