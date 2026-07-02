<?php

declare(strict_types=1);

namespace Modules\Authorization\Providers;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;

/**
 * Service provider for the Authorization module.
 *
 * Core tier (priority 1). RBAC policies and guards arrive in a later sprint;
 * for now it publishes its {@see ModuleInfo} marker so the platform can prove
 * the module booted.
 */
final class AuthorizationServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.authorization', new ModuleInfo('Authorization', '1.0.0'));
    }
}
