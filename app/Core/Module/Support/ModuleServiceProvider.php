<?php

declare(strict_types=1);

namespace App\Core\Module\Support;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Contracts\ModuleProvider;

/**
 * Base class for module service providers.
 *
 * Both phases default to no-ops so a scaffolded module boots cleanly before it
 * has any services to register. Concrete modules override {@see register()}
 * and/or {@see boot()}.
 */
abstract class ModuleServiceProvider implements ModuleProvider
{
    public function register(ContainerInterface $container): void
    {
    }

    public function boot(ContainerInterface $container): void
    {
    }
}
