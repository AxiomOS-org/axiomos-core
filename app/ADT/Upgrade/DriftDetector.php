<?php

declare(strict_types=1);

namespace App\ADT\Upgrade;

use App\ADT\MakeModule\ModuleBlueprintPlanner;

final class DriftDetector
{
    public function detect(string $modulePath, string $moduleName): DriftReport
    {
        $expected = array_map(
            static fn ($artifact): string => $artifact->relativePath,
            (new ModuleBlueprintPlanner())->plan($moduleName),
        );
        $expected = array_values(array_filter($expected, static fn (string $path): bool => $path !== '' && ! str_ends_with($path, '/')));

        $actual = $this->collectFiles($modulePath);
        $missing = array_values(array_diff($expected, $actual));
        $extra = array_values(array_diff($actual, $expected));

        return new DriftReport(
            moduleName: $moduleName,
            missingFiles: $missing,
            extraFiles: $extra,
            hasDrift: $missing !== [] || $extra !== [],
        );
    }

    /**
     * @return list<string>
     */
    private function collectFiles(string $modulePath): array
    {
        if (! is_dir($modulePath)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($modulePath, \FilesystemIterator::SKIP_DOTS),
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile()) {
                $relative = str_replace('\\', '/', substr($fileInfo->getPathname(), strlen($modulePath) + 1));
                $files[] = $relative;
            }
        }

        sort($files);

        return $files;
    }
}
