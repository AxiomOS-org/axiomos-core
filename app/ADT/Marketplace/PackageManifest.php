<?php

declare(strict_types=1);

namespace App\ADT\Marketplace;

use JsonException;
use RuntimeException;

/**
 * AMS-compatible marketplace package manifest.
 */
final class PackageManifest
{
    /**
     * @param list<string> $dependencies
     */
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $version,
        public readonly string $minimumCoreVersion,
        public readonly array $dependencies,
        public readonly string $checksum,
        public readonly string $path,
    ) {
    }

    public static function fromDirectory(string $packageDirectory): self
    {
        $manifestPath = $packageDirectory . DIRECTORY_SEPARATOR . 'package.json';

        if (! is_file($manifestPath)) {
            throw new RuntimeException("Package manifest not found: {$manifestPath}");
        }

        $contents = file_get_contents($manifestPath);

        if ($contents === false) {
            throw new RuntimeException("Unable to read package manifest: {$manifestPath}");
        }

        try {
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Invalid package manifest JSON', 0, $exception);
        }

        if (! is_array($data)) {
            throw new RuntimeException('Package manifest must decode to an object.');
        }

        foreach (['name', 'type', 'version', 'minimumCoreVersion'] as $field) {
            if (! isset($data[$field]) || ! is_string($data[$field])) {
                throw new RuntimeException("Package manifest missing field [{$field}]");
            }
        }

        $dependencies = [];

        if (isset($data['dependencies']) && is_array($data['dependencies'])) {
            $dependencies = array_values(array_filter($data['dependencies'], static fn (mixed $v): bool => is_string($v)));
        }

        $checksum = is_string($data['checksum'] ?? null) ? $data['checksum'] : '';

        return new self(
            name: $data['name'],
            type: $data['type'],
            version: $data['version'],
            minimumCoreVersion: $data['minimumCoreVersion'],
            dependencies: $dependencies,
            checksum: $checksum,
            path: $packageDirectory,
        );
    }
}
