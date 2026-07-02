<?php

declare(strict_types=1);

namespace App\Core\Module;

use App\Core\Module\Contracts\ProviderChecker;
use App\Core\Module\Events\ModuleDiscovered;
use App\Core\Module\Events\ModuleFailed;
use App\Core\Module\Events\ModuleLoaded;
use App\Core\Module\Exceptions\InvalidModuleException;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;
use JsonException;

/**
 * Discovers, validates and caches installed modules.
 *
 * Implements the "Discover Modules" stage of the kernel boot process. On a cold
 * boot the loader scans the modules directory, decodes and validates every
 * `module.json`, hydrates trustworthy {@see ModuleManifest} value objects and
 * caches the result. Subsequent requests read straight from the cache, so a
 * hundred modules are scanned once, not on every request:
 *
 *     Boot -> Scan -> Cache -> Next requests -> Read cache
 *
 * Folders without a manifest are ignored. Folders whose manifest is unreadable,
 * malformed, semantically invalid, or that conflict with another module raise a
 * meaningful {@see InvalidModuleException} (and emit {@see ModuleFailed}).
 */
final class ModuleLoader
{
    /**
     * Manifest fields that every module must declare.
     *
     * @var list<string>
     */
    private const REQUIRED_FIELDS = ['name', 'version', 'provider', 'enabled', 'dependencies', 'minimumCoreVersion'];

    /**
     * Cache key under which the discovered manifests are memoised.
     */
    private const CACHE_KEY = 'axiomos.modules.manifests';

    /**
     * Fixed namespace used to derive deterministic UUIDv5 identifiers.
     */
    private const UUID_NAMESPACE = '6f9619ff-8b86-d011-b42d-00c04fc964ff';

    /**
     * Official semantic-versioning pattern (semver.org).
     */
    private const SEMVER_PATTERN = '/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)'
        . '(?:-((?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?'
        . '(?:\+([0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$/';

    private const UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    /**
     * @param string              $modulesPath     Absolute path to the modules directory.
     * @param ProviderChecker     $providerChecker Verifies declared provider classes exist.
     * @param CacheRepository|null $cache          Optional cache; when null, every call rescans.
     * @param Dispatcher|null     $events          Optional event dispatcher for lifecycle events.
     * @param string              $coreVersion     Running kernel version for compatibility checks.
     */
    public function __construct(
        private readonly string $modulesPath,
        private readonly ProviderChecker $providerChecker,
        private readonly ?CacheRepository $cache = null,
        private readonly ?Dispatcher $events = null,
        private readonly string $coreVersion = '1.0.0',
    ) {
    }

    /**
     * Discover every valid module, reading from cache when available.
     *
     * @return Collection<int, ModuleManifest>
     *
     * @throws InvalidModuleException
     */
    public function discover(): Collection
    {
        $cached = $this->readCache();

        if ($cached !== null) {
            return $cached;
        }

        $manifests = $this->scan();

        $this->writeCache($manifests);

        return $manifests;
    }

    /**
     * Drop the cached manifests so the next discovery rescans from disk.
     */
    public function flush(): void
    {
        $this->cache?->forget(self::CACHE_KEY);
    }

    /**
     * Scan the modules directory and validate every declared module.
     *
     * @return Collection<int, ModuleManifest>
     *
     * @throws InvalidModuleException
     */
    private function scan(): Collection
    {
        $manifests = new Collection();

        foreach ($this->moduleDirectories() as $directory) {
            $manifestPath = $directory . DIRECTORY_SEPARATOR . 'module.json';

            if (! is_file($manifestPath)) {
                continue;
            }

            $manifests->push($this->loadModule($manifestPath, $directory));
        }

        $this->assertUniqueNames($manifests);
        $this->assertDependenciesResolvable($manifests);

        return $manifests->values();
    }

    /**
     * List the immediate module directories under the configured path.
     *
     * @return list<string>
     *
     * @throws InvalidModuleException
     */
    private function moduleDirectories(): array
    {
        if (! is_dir($this->modulesPath)) {
            throw InvalidModuleException::missingModulesPath($this->modulesPath);
        }

        $directories = glob($this->modulesPath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);

        return $directories === false ? [] : $directories;
    }

    /**
     * Decode, validate and hydrate a single module, emitting lifecycle events.
     *
     * @throws InvalidModuleException
     */
    private function loadModule(string $manifestPath, string $directory): ModuleManifest
    {
        try {
            $data = $this->decode($manifestPath);
            $this->events?->dispatch(new ModuleDiscovered($manifestPath));

            $this->assertRequiredFields($manifestPath, $data);
            $this->assertVersions($manifestPath, $data);
            $this->assertProviderExists($manifestPath, $data['provider']);
            $this->assertCoreCompatibility($manifestPath, $data);

            $manifest = $this->buildManifest($manifestPath, $data, $directory);
            $this->events?->dispatch(new ModuleLoaded($manifest));

            return $manifest;
        } catch (InvalidModuleException $exception) {
            $this->events?->dispatch(new ModuleFailed($manifestPath, $exception));

            throw $exception;
        }
    }

    /**
     * Decode a manifest file into an associative array.
     *
     * @return array<string, mixed>
     *
     * @throws InvalidModuleException
     */
    private function decode(string $manifestPath): array
    {
        $contents = file_get_contents($manifestPath);

        if ($contents === false) {
            throw InvalidModuleException::unreadableManifest($manifestPath);
        }

        try {
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw InvalidModuleException::malformedJson($manifestPath, $exception->getMessage());
        }

        if (! is_array($data)) {
            throw InvalidModuleException::malformedJson($manifestPath, 'Manifest must decode to an object.');
        }

        return $data;
    }

    /**
     * Guarantee that every required field is present and well-typed.
     *
     * @param array<string, mixed> $data
     *
     * @throws InvalidModuleException
     */
    private function assertRequiredFields(string $manifestPath, array $data): void
    {
        foreach (self::REQUIRED_FIELDS as $field) {
            if (! array_key_exists($field, $data)) {
                throw InvalidModuleException::missingField($manifestPath, $field);
            }
        }

        $this->assertNonEmptyString($manifestPath, $data['name'], 'name');
        $this->assertNonEmptyString($manifestPath, $data['version'], 'version');
        $this->assertNonEmptyString($manifestPath, $data['provider'], 'provider');

        if (! is_bool($data['enabled'])) {
            throw InvalidModuleException::invalidFieldType($manifestPath, 'enabled', 'boolean');
        }
    }

    /**
     * Assert a field is a non-empty string.
     *
     * @throws InvalidModuleException
     */
    private function assertNonEmptyString(string $manifestPath, mixed $value, string $field): void
    {
        if (! is_string($value)) {
            throw InvalidModuleException::invalidFieldType($manifestPath, $field, 'string');
        }

        if (trim($value) === '') {
            throw InvalidModuleException::emptyField($manifestPath, $field);
        }
    }

    /**
     * Validate the module version and, when present, the minimum core version.
     *
     * @param array<string, mixed> $data
     *
     * @throws InvalidModuleException
     */
    private function assertVersions(string $manifestPath, array $data): void
    {
        if (preg_match(self::SEMVER_PATTERN, (string) $data['version']) !== 1) {
            throw InvalidModuleException::invalidVersion($manifestPath, (string) $data['version']);
        }

        if (! array_key_exists('minimumCoreVersion', $data)) {
            return;
        }

        $minimum = $data['minimumCoreVersion'];

        if (! is_string($minimum) || preg_match(self::SEMVER_PATTERN, $minimum) !== 1) {
            throw InvalidModuleException::invalidVersion($manifestPath, (string) $minimum);
        }
    }

    /**
     * Ensure the declared provider class is resolvable.
     *
     * @throws InvalidModuleException
     */
    private function assertProviderExists(string $manifestPath, string $provider): void
    {
        if (! $this->providerChecker->exists($provider)) {
            throw InvalidModuleException::providerNotFound($manifestPath, $provider);
        }
    }

    /**
     * Ensure the running kernel satisfies the module's minimum core version.
     *
     * @param array<string, mixed> $data
     *
     * @throws InvalidModuleException
     */
    private function assertCoreCompatibility(string $manifestPath, array $data): void
    {
        if (! isset($data['minimumCoreVersion']) || ! is_string($data['minimumCoreVersion'])) {
            return;
        }

        if (version_compare($this->coreVersion, $data['minimumCoreVersion'], '<')) {
            throw InvalidModuleException::unsupportedCoreVersion(
                $manifestPath,
                $data['minimumCoreVersion'],
                $this->coreVersion,
            );
        }
    }

    /**
     * Reject two modules that declare the same name.
     *
     * @param Collection<int, ModuleManifest> $manifests
     *
     * @throws InvalidModuleException
     */
    private function assertUniqueNames(Collection $manifests): void
    {
        $seen = [];

        foreach ($manifests as $manifest) {
            if (isset($seen[$manifest->name])) {
                throw InvalidModuleException::duplicateName(
                    $manifest->name,
                    $seen[$manifest->name],
                    $manifest->path,
                );
            }

            $seen[$manifest->name] = $manifest->path;
        }
    }

    /**
     * Ensure every declared dependency resolves to a discovered module.
     *
     * @param Collection<int, ModuleManifest> $manifests
     *
     * @throws InvalidModuleException
     */
    private function assertDependenciesResolvable(Collection $manifests): void
    {
        $names = $manifests
            ->map(static fn (ModuleManifest $manifest): string => $manifest->name)
            ->flip();

        foreach ($manifests as $manifest) {
            foreach ($manifest->dependencies as $dependency) {
                if (! $names->has($dependency)) {
                    throw InvalidModuleException::missingDependency($manifest->name, $dependency);
                }
            }
        }
    }

    /**
     * Hydrate a validated manifest array into a value object.
     *
     * @param array<string, mixed> $data
     *
     * @throws InvalidModuleException
     */
    private function buildManifest(string $manifestPath, array $data, string $directory): ModuleManifest
    {
        $name = $data['name'];

        return new ModuleManifest(
            uuid: $this->resolveUuid($manifestPath, $data, $name),
            id: basename($directory),
            name: $name,
            version: $data['version'],
            provider: $data['provider'],
            enabled: $data['enabled'],
            priority: $this->readPriority($data),
            dependencies: $this->readStringList($data['dependencies'] ?? []),
            authors: $this->readAuthors($data['authors'] ?? []),
            path: $directory,
            minimumCoreVersion: isset($data['minimumCoreVersion']) && is_string($data['minimumCoreVersion'])
                ? $data['minimumCoreVersion']
                : null,
        );
    }

    /**
     * Resolve a stable UUID: use the declared one, or derive a deterministic v5.
     *
     * @param array<string, mixed> $data
     *
     * @throws InvalidModuleException
     */
    private function resolveUuid(string $manifestPath, array $data, string $name): string
    {
        if (! array_key_exists('uuid', $data)) {
            return $this->deterministicUuid($name);
        }

        $uuid = $data['uuid'];

        if (! is_string($uuid) || preg_match(self::UUID_PATTERN, $uuid) !== 1) {
            throw InvalidModuleException::invalidUuid($manifestPath, (string) $uuid);
        }

        return strtolower($uuid);
    }

    /**
     * Derive a stable UUIDv5 from the module name so identity survives reboots
     * even when a manifest omits an explicit UUID.
     */
    private function deterministicUuid(string $name): string
    {
        $namespace = str_replace('-', '', self::UUID_NAMESPACE);

        $binaryNamespace = '';
        for ($i = 0; $i < strlen($namespace); $i += 2) {
            $binaryNamespace .= chr((int) hexdec($namespace[$i] . $namespace[$i + 1]));
        }

        $hash = sha1($binaryNamespace . $name);

        return sprintf(
            '%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            substr($hash, 20, 12),
        );
    }

    /**
     * Read an optional integer priority, defaulting to 0 when absent or invalid.
     *
     * @param array<string, mixed> $data
     */
    private function readPriority(array $data): int
    {
        return isset($data['priority']) && is_int($data['priority']) ? $data['priority'] : 0;
    }

    /**
     * Normalise an optional list into a list of strings.
     *
     * @return list<string>
     */
    private function readStringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, static fn (mixed $item): bool => is_string($item)));
    }

    /**
     * Normalise the authors list into a list of arrays.
     *
     * @return list<array<string, mixed>>
     */
    private function readAuthors(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, static fn (mixed $item): bool => is_array($item)));
    }

    /**
     * Read the memoised manifests from cache, if a cache is configured and warm.
     *
     * @return Collection<int, ModuleManifest>|null
     */
    private function readCache(): ?Collection
    {
        if ($this->cache === null) {
            return null;
        }

        $cached = $this->cache->get(self::CACHE_KEY);

        if (! is_array($cached)) {
            return null;
        }

        return (new Collection($cached))->values();
    }

    /**
     * Persist the discovered manifests for subsequent requests.
     *
     * @param Collection<int, ModuleManifest> $manifests
     */
    private function writeCache(Collection $manifests): void
    {
        $this->cache?->forever(self::CACHE_KEY, $manifests->all());
    }
}
