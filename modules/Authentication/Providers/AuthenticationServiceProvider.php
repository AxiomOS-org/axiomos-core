<?php

declare(strict_types=1);

namespace Modules\Authentication\Providers;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;

/**
 * Service provider for the Authentication module.
 *
 * Core tier (priority 1). Full identity, token and session services arrive in
 * Sprint 5; for now it publishes its {@see ModuleInfo} marker so the platform
 * can prove the module booted.
 */
final class AuthenticationServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.authentication', new ModuleInfo('Authentication', '1.0.0'));
    }
}
