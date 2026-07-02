<?php

declare(strict_types=1);

namespace App\Core\Configuration\Loaders;

use App\Core\Configuration\ConfigurationSource;
use App\Core\Configuration\Contracts\ConfigurationLoaderInterface;
use App\Core\Configuration\Support\ArrayPath;

/**
 * Loads key/value pairs from a `.env` file into nested configuration.
 */
final class EnvironmentConfigurationLoader implements ConfigurationLoaderInterface
{
    public function __construct(
        private readonly string $envFilePath,
    ) {
    }

    public function source(): ConfigurationSource
    {
        return ConfigurationSource::Environment;
    }

    public function supports(): bool
    {
        return is_file($this->envFilePath) && is_readable($this->envFilePath);
    }

    public function load(): array
    {
        if (! $this->supports()) {
            return [];
        }

        $configuration = [];

        foreach ($this->parse(file_get_contents($this->envFilePath) ?: '') as $key => $value) {
            ArrayPath::set($configuration, $this->mapKey($key), $this->cast($value));
        }

        return $configuration;
    }

    /**
     * @return array<string, string>
     */
    private function parse(string $contents): array
    {
        $values = [];

        foreach (preg_split('/\R/', $contents) ?: [] as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_starts_with($line, 'export ')) {
                $line = trim(substr($line, 7));
            }

            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            $values[$key] = $this->stripQuotes($value);
        }

        return $values;
    }

    private function stripQuotes(string $value): string
    {
        if ($value === '') {
            return $value;
        }

        $first = $value[0];
        $last = $value[strlen($value) - 1];

        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            return substr($value, 1, -1);
        }

        return $value;
    }

    private function mapKey(string $envKey): string
    {
        $normalized = strtolower($envKey);

        return match (true) {
            str_starts_with($normalized, 'app_') => 'app.' . substr($normalized, 4),
            str_starts_with($normalized, 'db_') => 'database.' . substr($normalized, 3),
            str_starts_with($normalized, 'module_') => 'modules.' . substr($normalized, 7),
            str_starts_with($normalized, 'plugin_') => 'plugins.' . substr($normalized, 7),
            default => 'env.' . $normalized,
        };
    }

    private function cast(string $value): mixed
    {
        return match (strtolower($value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'null', '(null)' => null,
            'empty', '(empty)' => '',
            default => is_numeric($value) ? $value + 0 : $value,
        };
    }
}
