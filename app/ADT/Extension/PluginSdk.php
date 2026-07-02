<?php

declare(strict_types=1);

namespace App\ADT\Extension;

use App\ADT\Extension\Contracts\ExtensionProviderInterface;
use App\ADT\Extension\Contracts\ExtensionRegistryInterface;
use RuntimeException;

/**
 * Loads and registers plugin extension providers from the plugins directory.
 */
final class PluginSdk
{
    public function __construct(
        private readonly string $pluginsPath,
        private readonly string $coreVersion,
        private readonly ExtensionRegistryInterface $registry,
    ) {
    }

    /**
     * @return list<PluginManifest>
     */
    public function discover(): array
    {
        if (! is_dir($this->pluginsPath)) {
            return [];
        }

        $manifests = [];

        foreach (glob($this->pluginsPath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [] as $directory) {
            $manifestPath = $directory . DIRECTORY_SEPARATOR . 'plugin.json';

            if (! is_file($manifestPath)) {
                continue;
            }

            $manifest = PluginManifest::fromFile($manifestPath);
            $this->assertCoreCompatibility($manifest);
            $manifests[] = $manifest;
        }

        return $manifests;
    }

    public function loadAll(): int
    {
        $loaded = 0;

        foreach ($this->discover() as $manifest) {
            $this->loadProvider($manifest);
            ++$loaded;
        }

        return $loaded;
    }

    public function negotiateCapabilities(PluginManifest $manifest, array $required): bool
    {
        if ($required === []) {
            return true;
        }

        return array_diff($required, $manifest->capabilities) === [];
    }

    private function loadProvider(PluginManifest $manifest): void
    {
        $providerClass = $manifest->provider;

        if (! class_exists($providerClass)) {
            throw new RuntimeException("Plugin provider class not found: {$providerClass}");
        }

        $provider = new $providerClass();

        if (! $provider instanceof ExtensionProviderInterface) {
            throw new RuntimeException("Plugin provider must implement ExtensionProviderInterface: {$providerClass}");
        }

        $provider->register($this->registry);
    }

    private function assertCoreCompatibility(PluginManifest $manifest): void
    {
        if (version_compare($this->coreVersion, $manifest->minimumCoreVersion, '<')) {
            throw new RuntimeException(sprintf(
                'Plugin [%s] requires core >= %s, running %s',
                $manifest->name,
                $manifest->minimumCoreVersion,
                $this->coreVersion,
            ));
        }
    }
}
