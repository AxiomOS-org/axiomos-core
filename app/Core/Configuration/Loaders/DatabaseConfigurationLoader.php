<?php

declare(strict_types=1);

namespace App\Core\Configuration\Loaders;

use App\Core\Configuration\ConfigurationSource;
use App\Core\Configuration\Contracts\ConfigurationLoaderInterface;

/**
 * Loads configuration persisted in the database via an injected resolver.
 */
final class DatabaseConfigurationLoader implements ConfigurationLoaderInterface
{
    /**
     * @param (callable(): array<string, mixed>)|null $resolver
     */
    public function __construct(private $resolver = null)
    {
    }

    public function source(): ConfigurationSource
    {
        return ConfigurationSource::Database;
    }

    public function supports(): bool
    {
        return $this->resolver !== null;
    }

    public function load(): array
    {
        if (! $this->supports()) {
            return [];
        }

        $payload = ($this->resolver)();

        return is_array($payload) ? $payload : [];
    }
}
