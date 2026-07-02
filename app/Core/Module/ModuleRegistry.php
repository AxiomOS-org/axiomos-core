<?php

declare(strict_types=1);

namespace App\Core\Module;

use App\Core\Module\Events\ModuleDisabled;
use App\Core\Module\Events\ModuleEnabled;
use App\Core\Module\Events\ModuleRegistered;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use LogicException;

/**
 * In-memory authority for the modules known to the running kernel.
 *
 * The registry is the single source of truth for module identity and state
 * after discovery. It accepts validated {@see ModuleManifest} value objects,
 * guarantees each UUID and name is registered at most once, exposes lookups by
 * UUID and name, partitions modules by enabled state, and toggles state while
 * preserving manifest immutability (each toggle stores a fresh manifest).
 *
 * State transitions emit domain events ({@see ModuleRegistered},
 * {@see ModuleEnabled}, {@see ModuleDisabled}) so the marketplace, audit trail
 * and boot manager can react without the registry knowing about them.
 */
final class ModuleRegistry
{
    /**
     * Registered manifests keyed by lower-cased UUID.
     *
     * @var array<string, ModuleManifest>
     */
    private array $modules = [];

    /**
     * Lower-cased name => UUID index for name lookups and duplicate detection.
     *
     * @var array<string, string>
     */
    private array $namesToUuid = [];

    public function __construct(private readonly ?Dispatcher $events = null)
    {
    }

    /**
     * Register a discovered module.
     *
     * @throws LogicException When the UUID or name is already registered.
     */
    public function register(ModuleManifest $manifest): void
    {
        $uuid = $this->key($manifest->uuid);
        $name = $this->nameKey($manifest->name);

        if (isset($this->modules[$uuid])) {
            throw new LogicException(sprintf('Module with UUID "%s" is already registered.', $manifest->uuid));
        }

        if (isset($this->namesToUuid[$name])) {
            throw new LogicException(sprintf('Module named "%s" is already registered.', $manifest->name));
        }

        $this->modules[$uuid] = $manifest;
        $this->namesToUuid[$name] = $uuid;

        $this->events?->dispatch(new ModuleRegistered($manifest));
    }

    /**
     * All registered modules, in registration order.
     *
     * @return Collection<int, ModuleManifest>
     */
    public function all(): Collection
    {
        return (new Collection(array_values($this->modules)));
    }

    public function has(string $uuid): bool
    {
        return isset($this->modules[$this->key($uuid)]);
    }

    public function findByUuid(string $uuid): ?ModuleManifest
    {
        return $this->modules[$this->key($uuid)] ?? null;
    }

    public function findByName(string $name): ?ModuleManifest
    {
        $uuid = $this->namesToUuid[$this->nameKey($name)] ?? null;

        return $uuid === null ? null : $this->modules[$uuid];
    }

    /**
     * @return Collection<int, ModuleManifest>
     */
    public function enabled(): Collection
    {
        return $this->all()->filter(static fn (ModuleManifest $m): bool => $m->enabled)->values();
    }

    /**
     * @return Collection<int, ModuleManifest>
     */
    public function disabled(): Collection
    {
        return $this->all()->reject(static fn (ModuleManifest $m): bool => $m->enabled)->values();
    }

    /**
     * Enable a module by UUID. Idempotent; only fires an event on transition.
     *
     * @throws InvalidArgumentException When the module is not registered.
     */
    public function enable(string $uuid): ModuleManifest
    {
        $manifest = $this->require($uuid);

        if ($manifest->enabled) {
            return $manifest;
        }

        $updated = $manifest->withEnabled(true);
        $this->modules[$this->key($uuid)] = $updated;

        $this->events?->dispatch(new ModuleEnabled($updated));

        return $updated;
    }

    /**
     * Disable a module by UUID. Idempotent; only fires an event on transition.
     *
     * @throws InvalidArgumentException When the module is not registered.
     */
    public function disable(string $uuid): ModuleManifest
    {
        $manifest = $this->require($uuid);

        if (! $manifest->enabled) {
            return $manifest;
        }

        $updated = $manifest->withEnabled(false);
        $this->modules[$this->key($uuid)] = $updated;

        $this->events?->dispatch(new ModuleDisabled($updated));

        return $updated;
    }

    /**
     * Remove a module from the registry entirely.
     *
     * @throws InvalidArgumentException When the module is not registered.
     */
    public function remove(string $uuid): void
    {
        $manifest = $this->require($uuid);

        unset($this->modules[$this->key($uuid)], $this->namesToUuid[$this->nameKey($manifest->name)]);
    }

    /**
     * Fetch a registered manifest or fail loudly.
     *
     * @throws InvalidArgumentException
     */
    private function require(string $uuid): ModuleManifest
    {
        return $this->findByUuid($uuid)
            ?? throw new InvalidArgumentException(sprintf('Module with UUID "%s" is not registered.', $uuid));
    }

    private function key(string $uuid): string
    {
        return strtolower($uuid);
    }

    private function nameKey(string $name): string
    {
        return strtolower($name);
    }
}
