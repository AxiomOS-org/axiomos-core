<?php

declare(strict_types=1);

namespace App\Core\Module;

/**
 * Immutable, validated representation of a single `module.json` file.
 *
 * Produced exclusively by {@see ModuleLoader} after a manifest has passed full
 * structural, semantic and cross-module validation, so any instance is
 * guaranteed to carry a stable UUID, a unique name, a semantic version, a
 * loadable provider and an explicit enabled flag. The object is behaviour-free:
 * it is a value object consumed by the registry, boot manager and (future)
 * marketplace.
 */
final readonly class ModuleManifest
{
    /**
     * @param string                      $uuid              Stable global identifier (marketplace).
     * @param string                      $id                Local identifier (module directory name).
     * @param string                      $name              Unique, human-facing module name.
     * @param string                      $version           Semantic version of the module.
     * @param string                      $provider          Fully-qualified service provider class name.
     * @param bool                        $enabled           Whether the module participates in boot.
     * @param int                         $priority          Lower values boot earlier.
     * @param list<string>                $dependencies      Names of modules that must load first.
     * @param list<array<string, mixed>>  $authors           Author metadata as declared in the manifest.
     * @param string                      $path              Absolute path to the module directory on disk.
     * @param string|null                 $minimumCoreVersion Minimum kernel version the module supports.
     */
    public function __construct(
        public string $uuid,
        public string $id,
        public string $name,
        public string $version,
        public string $provider,
        public bool $enabled,
        public int $priority,
        public array $dependencies,
        public array $authors,
        public string $path,
        public ?string $minimumCoreVersion = null,
    ) {
    }

    /**
     * Return a copy of this manifest with a different enabled state.
     *
     * Immutability is preserved: the current instance is never mutated; a new
     * value object is returned instead. Used by the registry to toggle a module
     * without leaking mutable state into the discovery layer.
     */
    public function withEnabled(bool $enabled): self
    {
        return new self(
            uuid: $this->uuid,
            id: $this->id,
            name: $this->name,
            version: $this->version,
            provider: $this->provider,
            enabled: $enabled,
            priority: $this->priority,
            dependencies: $this->dependencies,
            authors: $this->authors,
            path: $this->path,
            minimumCoreVersion: $this->minimumCoreVersion,
        );
    }
}
