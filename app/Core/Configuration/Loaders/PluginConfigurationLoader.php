<?php

declare(strict_types=1);

namespace App\Core\Configuration\Loaders;

use App\Core\Configuration\ConfigurationSource;
use App\Core\Configuration\Contracts\ConfigurationLoaderInterface;
use App\Core\Configuration\Support\ArrayPath;

/**
 * Loads configuration shipped by installed plugins.
 */
final class PluginConfigurationLoader implements ConfigurationLoaderInterface
{
    public function __construct(private readonly string $pluginsPath)
    {
    }

    public function source(): ConfigurationSource
    {
        return ConfigurationSource::Plugin;
    }

    public function supports(): bool
    {
        return is_dir($this->pluginsPath);
    }

    public function load(): array
    {
        if (! $this->supports()) {
            return [];
        }

        $configuration = ['plugins' => []];

        foreach (glob($this->pluginsPath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [] as $pluginDirectory) {
            $pluginName = basename($pluginDirectory);
            $configDirectory = $pluginDirectory . DIRECTORY_SEPARATOR . 'Config';

            if (! is_dir($configDirectory)) {
                continue;
            }

            foreach (glob($configDirectory . DIRECTORY_SEPARATOR . '*.php') ?: [] as $file) {
                /** @var mixed $payload */
                $payload = require $file;

                if (! is_array($payload)) {
                    continue;
                }

                $configuration['plugins'][$pluginName] = ArrayPath::merge(
                    $configuration['plugins'][$pluginName] ?? [],
                    $payload,
                );
            }
        }

        return $configuration;
    }
}
