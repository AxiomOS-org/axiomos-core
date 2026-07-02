<?php

declare(strict_types=1);

namespace App\ADT\MakeModule;

/**
 * Detects naming and filesystem conflicts before module generation.
 */
final class ModuleConflictDetector
{
    public function __construct(
        private readonly string $modulesPath,
    ) {
    }

    /**
     * @return list<string>
     */
    public function detect(string $moduleName): array
    {
        $conflicts = [];
        $targetPath = $this->modulesPath . DIRECTORY_SEPARATOR . $moduleName;

        if (is_dir($targetPath) || is_file($targetPath)) {
            $conflicts[] = "Module path already exists: modules/{$moduleName}";
        }

        foreach ($this->existingModuleNames() as $existingName) {
            if (strcasecmp($existingName, $moduleName) === 0) {
                $conflicts[] = "Module name already registered: {$existingName}";
            }
        }

        return $conflicts;
    }

    /**
     * @return list<string>
     */
    private function existingModuleNames(): array
    {
        if (! is_dir($this->modulesPath)) {
            return [];
        }

        $names = [];

        foreach (glob($this->modulesPath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [] as $directory) {
            $manifestPath = $directory . DIRECTORY_SEPARATOR . 'module.json';

            if (! is_file($manifestPath)) {
                continue;
            }

            $contents = file_get_contents($manifestPath);

            if ($contents === false) {
                continue;
            }

            $data = json_decode($contents, true);

            if (is_array($data) && isset($data['name']) && is_string($data['name'])) {
                $names[] = $data['name'];
            }
        }

        return $names;
    }
}
