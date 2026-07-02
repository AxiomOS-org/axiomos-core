<?php

declare(strict_types=1);

namespace App\ADT\MakeModule;

/**
 * A single file or directory entry in a module blueprint plan.
 */
final class BlueprintArtifact
{
    public function __construct(
        public readonly string $relativePath,
        public readonly string $content,
        public readonly bool $isDirectory = false,
    ) {
    }
}
