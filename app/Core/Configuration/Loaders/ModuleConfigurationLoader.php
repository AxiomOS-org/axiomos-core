<?php

declare(strict_types=1);

namespace App\Core\Configuration\Loaders;

use App\Core\Configuration\ConfigurationSource;
use App\Core\Configuration\Contracts\ConfigurationLoaderInterface;
use App\Core\Configuration\Support\ArrayPath;

/**
 * Loads configuration shipped by installed modules.
 */
final class ModuleConfigurationLoader implements ConfigurationLoaderInterface
{
    public function __construct(private readonly string $modulesPath)
    {
    }

    public function source(): ConfigurationSource
    {
        return ConfigurationSource::Module;
    }

    public function supports(): bool
    {
        return is_dir($this->modulesPath);
    }

    public function load(): array
    {
        if (! $this->supports()) {
            return [];
        }

        $configuration = ['modules' => []];

        foreach (glob($this->modulesPath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [] as $moduleDirectory) {
            $moduleName = basename($moduleDirectory);
            $configDirectory = $moduleDirectory . DIRECTORY_SEPARATOR . 'Config';

            if (! is_dir($configDirectory)) {
                continue;
            }

            foreach (glob($configDirectory . DIRECTORY_SEPARATOR . '*.php') ?: [] as $file) {
                /** @var mixed $payload */
                $payload = require $file;

                if (! is_array($payload)) {
                    continue;
                }

                $configuration['modules'][$moduleName] = ArrayPath::merge(
                    $configuration['modules'][$moduleName] ?? [],
                    $payload,
                );
            }
        }

        return $configuration;
    }
}
