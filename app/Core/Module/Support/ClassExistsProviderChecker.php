<?php

declare(strict_types=1);

namespace App\Core\Module\Support;

use App\Core\Module\Contracts\ProviderChecker;

/**
 * Production {@see ProviderChecker} that verifies a provider class is resolvable
 * through the active autoloader.
 */
final class ClassExistsProviderChecker implements ProviderChecker
{
    public function exists(string $providerClass): bool
    {
        return class_exists($providerClass);
    }
}
