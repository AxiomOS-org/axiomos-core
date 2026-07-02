<?php

declare(strict_types=1);

namespace App\Core\Configuration\Loaders;

use App\Core\Configuration\ConfigurationSource;
use App\Core\Configuration\Contracts\ConfigurationLoaderInterface;
use App\Core\Configuration\Exceptions\ConfigurationException;
use App\Core\Configuration\Support\ArrayPath;

/**
 * Loads PHP and JSON configuration files from a directory.
 */
final class FileConfigurationLoader implements ConfigurationLoaderInterface
{
    public function __construct(private readonly string $configPath)
    {
    }

    public function source(): ConfigurationSource
    {
        return ConfigurationSource::File;
    }

    public function supports(): bool
    {
        return is_dir($this->configPath);
    }

    public function load(): array
    {
        if (! $this->supports()) {
            return [];
        }

        $configuration = [];

        foreach (glob($this->configPath . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            if (! is_file($file)) {
                continue;
            }

            $name = pathinfo($file, PATHINFO_FILENAME);
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            $payload = match ($extension) {
                'php' => $this->loadPhp($file),
                'json' => $this->loadJson($file),
                default => null,
            };

            if (! is_array($payload)) {
                continue;
            }

            $configuration[$name] = ArrayPath::merge($configuration[$name] ?? [], $payload);
        }

        return $configuration;
    }

    /**
     * @return array<string, mixed>
     */
    private function loadPhp(string $file): array
    {
        /** @var mixed $payload */
        $payload = require $file;

        if (! is_array($payload)) {
            throw new ConfigurationException(sprintf('Configuration file "%s" must return an array.', $file));
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private function loadJson(string $file): array
    {
        $contents = file_get_contents($file);

        if ($contents === false) {
            throw new ConfigurationException(sprintf('Unable to read configuration file "%s".', $file));
        }

        $payload = json_decode($contents, true);

        if (! is_array($payload)) {
            throw new ConfigurationException(sprintf('Configuration file "%s" contains invalid JSON.', $file));
        }

        return $payload;
    }
}
