<?php

declare(strict_types=1);

namespace Tests\Support\QA;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Static architecture probes for DDD / hexagonal boundaries.
 */
final class ArchitectureScanner
{
    /**
     * @var array<string, list<string>>
     */
    private const FORBIDDEN_LAYER_IMPORTS = [
        'Domain' => [
            'Infrastructure\\',
            'Http\\',
        ],
        'Application' => [
            'Infrastructure\\Persistence\\',
        ],
    ];

    /**
     * @return list<string>
     */
    public static function scanLayerViolations(string $basePath): array
    {
        $violations = [];
        $modulesPath = $basePath . DIRECTORY_SEPARATOR . 'modules';

        foreach (glob($modulesPath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [] as $moduleDirectory) {
            foreach (self::FORBIDDEN_LAYER_IMPORTS as $layer => $forbiddenPrefixes) {
                $layerPath = $moduleDirectory . DIRECTORY_SEPARATOR . $layer;

                if (! is_dir($layerPath)) {
                    continue;
                }

                foreach (self::phpFiles($layerPath) as $file) {
                    $content = file_get_contents($file);

                    if (! is_string($content)) {
                        continue;
                    }

                    foreach ($forbiddenPrefixes as $prefix) {
                        if (preg_match('/^use\s+[^;]*' . preg_quote($prefix, '/') . '/m', $content) === 1) {
                            $violations[] = sprintf('%s imports forbidden prefix %s', self::relative($basePath, $file), $prefix);
                        }
                    }
                }
            }
        }

        return $violations;
    }

    /**
     * @return list<string>
     */
    public static function scanEloquentInDomain(string $basePath): array
    {
        $violations = [];
        $modulesPath = $basePath . DIRECTORY_SEPARATOR . 'modules';

        foreach (glob($modulesPath . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'Domain', GLOB_ONLYDIR) ?: [] as $domainPath) {
            foreach (self::phpFiles($domainPath) as $file) {
                $basename = basename($file);

                if (str_starts_with($basename, 'Eloquent') && str_contains($basename, 'Repository')) {
                    $violations[] = self::relative($basePath, $file) . ' must not live in Domain';
                }
            }
        }

        return $violations;
    }

    /**
     * @return array{name: string, dependencies: list<string>}[]
     */
    public static function moduleManifests(string $basePath): array
    {
        $manifests = [];

        foreach (glob($basePath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'module.json') ?: [] as $manifestFile) {
            /** @var array<string, mixed> $decoded */
            $decoded = json_decode((string) file_get_contents($manifestFile), true, 512, JSON_THROW_ON_ERROR);
            $dependencies = $decoded['dependencies'] ?? [];

            $manifests[] = [
                'name' => (string) ($decoded['name'] ?? basename(dirname($manifestFile))),
                'dependencies' => is_array($dependencies) ? array_values(array_map('strval', $dependencies)) : [],
            ];
        }

        return $manifests;
    }

    /**
     * @param array{name: string, dependencies: list<string>}[] $manifests
     *
     * @return list<string>
     */
    public static function detectCircularModuleDependencies(array $manifests): array
    {
        $graph = [];

        foreach ($manifests as $manifest) {
            $graph[$manifest['name']] = $manifest['dependencies'];
        }

        $cycles = [];
        $visited = [];
        $stack = [];

        $visit = static function (string $node) use (&$visit, &$graph, &$visited, &$stack, &$cycles): void {
            if (isset($stack[$node])) {
                $cycles[] = $node . ' -> circular dependency detected';

                return;
            }

            if (isset($visited[$node])) {
                return;
            }

            $visited[$node] = true;
            $stack[$node] = true;

            foreach ($graph[$node] ?? [] as $dependency) {
                if (isset($graph[$dependency])) {
                    $visit($dependency);
                }
            }

            unset($stack[$node]);
        };

        foreach (array_keys($graph) as $module) {
            $visit($module);
        }

        return array_values(array_unique($cycles));
    }

    /**
     * @return list<string>
     */
    public static function scanDuplicateFiles(string $basePath): array
    {
        $hashes = [];
        $duplicates = [];

        foreach (['app', 'modules'] as $root) {
            $directory = $basePath . DIRECTORY_SEPARATOR . $root;

            if (! is_dir($directory)) {
                continue;
            }

            foreach (self::phpFiles($directory) as $file) {
                if (str_contains($file, DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR)) {
                    continue;
                }

                $normalized = preg_replace('/\s+/', ' ', trim((string) file_get_contents($file))) ?? '';
                $hash = sha1($normalized);

                if (isset($hashes[$hash]) && basename($hashes[$hash]) === basename($file)) {
                    $duplicates[] = sprintf('Duplicate content: %s and %s', self::relative($basePath, $hashes[$hash]), self::relative($basePath, $file));
                }

                $hashes[$hash] = $file;
            }
        }

        return $duplicates;
    }

    /**
     * @return list<string>
     */
    private static function phpFiles(string $directory): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private static function relative(string $basePath, string $file): string
    {
        return str_replace($basePath . DIRECTORY_SEPARATOR, '', $file);
    }
}
