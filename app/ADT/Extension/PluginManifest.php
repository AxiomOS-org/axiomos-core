<?php

declare(strict_types=1);

namespace App\ADT\Extension;

use JsonException;
use RuntimeException;

/**
 * Parsed plugin manifest (AMS-compatible subset).
 */
final class PluginManifest
{
    /**
     * @param list<string> $capabilities
     */
    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly string $provider,
        public readonly array $capabilities,
        public readonly string $minimumCoreVersion,
        public readonly string $path,
    ) {
    }

    public static function fromFile(string $manifestPath): self
    {
        $contents = file_get_contents($manifestPath);

        if ($contents === false) {
            throw new RuntimeException("Unable to read plugin manifest: {$manifestPath}");
        }

        try {
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException("Invalid plugin manifest JSON: {$manifestPath}", 0, $exception);
        }

        if (! is_array($data)) {
            throw new RuntimeException("Plugin manifest must decode to an object: {$manifestPath}");
        }

        foreach (['name', 'version', 'provider', 'minimumCoreVersion'] as $field) {
            if (! isset($data[$field]) || ! is_string($data[$field]) || trim($data[$field]) === '') {
                throw new RuntimeException("Plugin manifest missing field [{$field}]: {$manifestPath}");
            }
        }

        $capabilities = [];

        if (isset($data['capabilities']) && is_array($data['capabilities'])) {
            $capabilities = array_values(array_filter($data['capabilities'], static fn (mixed $v): bool => is_string($v)));
        }

        return new self(
            name: $data['name'],
            version: $data['version'],
            provider: $data['provider'],
            capabilities: $capabilities,
            minimumCoreVersion: $data['minimumCoreVersion'],
            path: dirname($manifestPath),
        );
    }
}
