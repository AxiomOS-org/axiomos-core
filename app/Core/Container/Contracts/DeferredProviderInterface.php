<?php

declare(strict_types=1);

namespace App\Core\Container\Contracts;

/**
 * Providers whose services are registered only when first requested.
 */
interface DeferredProviderInterface extends ServiceProviderInterface
{
}
