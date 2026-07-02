<?php

declare(strict_types=1);

namespace App\Core\Container\Contracts;

/**
 * Registers services with the container during the register phase.
 */
interface ServiceProviderInterface
{
    public function register(ContainerInterface $container): void;

    /**
     * @return list<string>
     */
    public function provides(): array;
}
