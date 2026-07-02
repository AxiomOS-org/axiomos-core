<?php

declare(strict_types=1);

namespace App\Core\Configuration\Loaders;

use App\Core\Configuration\ConfigurationSource;
use App\Core\Configuration\Contracts\ConfigurationLoaderInterface;
use App\Core\Configuration\Support\ArrayPath;

/**
 * In-memory runtime overrides with the highest precedence.
 */
final class RuntimeConfigurationLoader implements ConfigurationLoaderInterface
{
    /** @var array<string, mixed> */
    private array $items = [];

    public function source(): ConfigurationSource
    {
        return ConfigurationSource::Runtime;
    }

    public function supports(): bool
    {
        return true;
    }

    public function load(): array
    {
        return $this->items;
    }

    public function set(string $key, mixed $value): void
    {
        ArrayPath::set($this->items, $key, $value);
    }

    public function clear(): void
    {
        $this->items = [];
    }
}
