<?php

declare(strict_types=1);

namespace App\Core\Configuration;

use App\Core\Configuration\Contracts\ConfigurationLoaderInterface;
use App\Core\Configuration\Contracts\ConfigurationManager as ConfigurationManagerContract;
use App\Core\Configuration\Contracts\ConfigurationValidatorInterface;
use App\Core\Configuration\Events\ConfigurationLoaded;
use App\Core\Configuration\Events\ConfigurationLoading;
use App\Core\Configuration\Events\ConfigurationReloaded;
use App\Core\Configuration\Events\ConfigurationReloading;
use App\Core\Configuration\Events\ConfigurationValidationFailed;
use App\Core\Configuration\Exceptions\ConfigurationException;
use App\Core\Configuration\Exceptions\InvalidConfigurationException;
use App\Core\Configuration\Loaders\RuntimeConfigurationLoader;
use App\Core\Configuration\Support\ArrayPath;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Enterprise configuration manager for AxiomOS.
 *
 * Merges configuration from files, environment, database, modules, plugins and
 * runtime overrides; validates the result; supports caching and hot reload.
 */
final class ConfigurationManager implements ConfigurationManagerContract
{
    /** @var array<string, mixed> */
    private array $items = [];

    /** @var array<string, ConfigurationSource> */
    private array $sources = [];

    private bool $loaded = false;

    /**
     * @param list<ConfigurationLoaderInterface> $loaders
     */
    public function __construct(
        private readonly array $loaders,
        private readonly ConfigurationValidatorInterface $validator,
        private readonly RuntimeConfigurationLoader $runtimeLoader,
        private readonly ?Dispatcher $events = null,
        private readonly ?string $defaultCachePath = null,
    ) {
    }

    public function load(): void
    {
        if ($this->loaded) {
            return;
        }

        $this->items = $this->mergeFromLoaders();
        $this->validateConfiguration();
        $this->loaded = true;

        $this->events?->dispatch(new ConfigurationLoaded($this->items));
    }

    public function reload(): void
    {
        $this->events?->dispatch(new ConfigurationReloading());

        $this->loaded = false;
        $this->items = [];
        $this->sources = [];

        $this->load();

        $this->events?->dispatch(new ConfigurationReloaded($this->items));
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->ensureLoaded();

        return ArrayPath::get($this->items, $key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        $this->runtimeLoader->set($key, $value);
        ArrayPath::set($this->items, $key, $value);
        $this->sources[$key] = ConfigurationSource::Runtime;
    }

    public function has(string $key): bool
    {
        $this->ensureLoaded();

        return ArrayPath::has($this->items, $key);
    }

    public function all(): array
    {
        $this->ensureLoaded();

        return $this->items;
    }

    public function sourceOf(string $key): ?ConfigurationSource
    {
        $this->ensureLoaded();

        return $this->sources[$key] ?? null;
    }

    public function cache(?string $path = null): void
    {
        $path ??= $this->defaultCachePath;

        if ($path === null) {
            throw new ConfigurationException('Configuration cache path is not configured.');
        }

        $this->ensureLoaded();

        $exported = var_export([
            'items' => $this->items,
            'sources' => array_map(static fn (ConfigurationSource $source): string => $source->value, $this->sources),
        ], true);

        file_put_contents($path, "<?php\n\ndeclare(strict_types=1);\n\nreturn {$exported};\n");
    }

    public function loadCache(string $path): bool
    {
        if (! is_file($path)) {
            return false;
        }

        /** @var array{items: array<string, mixed>, sources: array<string, string>} $payload */
        $payload = require $path;

        $this->items = $payload['items'];
        $this->sources = [];

        foreach ($payload['sources'] as $key => $source) {
            $this->sources[$key] = ConfigurationSource::from($source);
        }

        $this->loaded = true;

        return true;
    }

    public function clearCache(string $path): void
    {
        if (is_file($path)) {
            unlink($path);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function mergeFromLoaders(): array
    {
        $items = [];
        $sources = [];

        $sorted = $this->loaders;
        usort(
            $sorted,
            static fn (ConfigurationLoaderInterface $a, ConfigurationLoaderInterface $b): int =>
                $a->source()->priority() <=> $b->source()->priority(),
        );

        foreach ($sorted as $loader) {
            if (! $loader->supports()) {
                continue;
            }

            $this->events?->dispatch(new ConfigurationLoading($loader->source()));

            $payload = $loader->load();
            $items = ArrayPath::merge($items, $payload);
            $this->trackSources($sources, $payload, $loader->source());
        }

        $this->sources = $sources;

        return $items;
    }

    /**
     * @param array<string, ConfigurationSource> $sources
     * @param array<string, mixed>               $payload
     */
    private function trackSources(array &$sources, array $payload, ConfigurationSource $source, string $prefix = ''): void
    {
        foreach ($payload as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix . '.' . $key;
            $sources[$path] = $source;

            if (is_array($value)) {
                $this->trackSources($sources, $value, $source, $path);
            }
        }
    }

    private function validateConfiguration(): void
    {
        try {
            $this->validator->validate($this->items);
        } catch (InvalidConfigurationException $exception) {
            $this->events?->dispatch(new ConfigurationValidationFailed($exception->violations));

            throw $exception;
        }
    }

    private function ensureLoaded(): void
    {
        if (! $this->loaded) {
            $this->load();
        }
    }
}
