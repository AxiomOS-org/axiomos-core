<?php

declare(strict_types=1);

namespace App\ADT\MakeModule;

/**
 * In-memory simulation report for module blueprint generation.
 */
final class SimulationReport
{
    /**
     * @param list<string> $conflicts
     * @param list<string> $dependencies
     */
    public function __construct(
        public readonly string $moduleName,
        public readonly int $directoryCount,
        public readonly int $fileCount,
        public readonly int $routeCount,
        public readonly int $entityCount,
        public readonly int $controllerCount,
        public readonly float $estimatedSeconds,
        public readonly array $conflicts,
        public readonly array $dependencies,
        public readonly bool $ready,
    ) {
    }

    /**
     * @param list<BlueprintArtifact> $artifacts
     */
    public static function fromArtifacts(string $moduleName, array $artifacts, array $conflicts): self
    {
        $directories = 0;
        $files = 0;

        foreach ($artifacts as $artifact) {
            if ($artifact->isDirectory) {
                ++$directories;
            } else {
                ++$files;
            }
        }

        $ready = $conflicts === [];

        return new self(
            moduleName: $moduleName,
            directoryCount: $directories,
            fileCount: $files,
            routeCount: 0,
            entityCount: 0,
            controllerCount: 0,
            estimatedSeconds: max(1.0, round($files * 0.05, 1)),
            conflicts: $conflicts,
            dependencies: ['Platform', 'Core'],
            ready: $ready,
        );
    }
}
