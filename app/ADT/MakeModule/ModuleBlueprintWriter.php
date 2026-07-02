<?php

declare(strict_types=1);

namespace App\ADT\MakeModule;

use RuntimeException;

/**
 * Writes an approved module blueprint to disk.
 */
final class ModuleBlueprintWriter
{
    public function __construct(
        private readonly string $modulesPath,
    ) {
    }

    /**
     * @param list<BlueprintArtifact> $artifacts
     */
    public function write(string $moduleName, array $artifacts): string
    {
        $modulePath = $this->modulesPath . DIRECTORY_SEPARATOR . $moduleName;

        if (is_dir($modulePath)) {
            throw new RuntimeException("Cannot write module; path already exists: modules/{$moduleName}");
        }

        if (! mkdir($modulePath, 0777, true) && ! is_dir($modulePath)) {
            throw new RuntimeException("Failed to create module directory: modules/{$moduleName}");
        }

        foreach ($artifacts as $artifact) {
            if ($artifact->relativePath === '') {
                continue;
            }

            $targetPath = $modulePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $artifact->relativePath);

            if ($artifact->isDirectory) {
                if (! is_dir($targetPath) && ! mkdir($targetPath, 0777, true) && ! is_dir($targetPath)) {
                    throw new RuntimeException("Failed to create directory: {$artifact->relativePath}");
                }

                continue;
            }

            $parent = dirname($targetPath);

            if (! is_dir($parent) && ! mkdir($parent, 0777, true) && ! is_dir($parent)) {
                throw new RuntimeException("Failed to create parent directory for: {$artifact->relativePath}");
            }

            if (file_put_contents($targetPath, $artifact->content) === false) {
                throw new RuntimeException("Failed to write file: {$artifact->relativePath}");
            }
        }

        return $modulePath;
    }
}
