<?php

declare(strict_types=1);

namespace App\Core\Module\Contracts;

/**
 * Abstraction over "does this service provider class exist and is it loadable?".
 *
 * Kept behind an interface so the discovery layer depends on an abstraction
 * (Dependency Inversion) rather than the global `class_exists()` function,
 * which keeps validation deterministically testable with fixture manifests.
 */
interface ProviderChecker
{
    public function exists(string $providerClass): bool;
}
