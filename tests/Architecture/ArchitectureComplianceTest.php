<?php

declare(strict_types=1);

namespace Tests\Architecture;

use Tests\Support\PostgresFeatureTestCase;
use Tests\Support\QA\ArchitectureScanner;

final class ArchitectureComplianceTest extends PostgresFeatureTestCase
{
    public function test_domain_layer_has_no_forbidden_imports(): void
    {
        $violations = ArchitectureScanner::scanLayerViolations($this->basePath);

        self::assertSame([], $violations, implode("\n", $violations));
    }

    public function test_no_eloquent_repositories_in_domain(): void
    {
        $violations = ArchitectureScanner::scanEloquentInDomain($this->basePath);

        self::assertSame([], $violations, implode("\n", $violations));
    }

    public function test_module_dependency_graph_has_no_cycles(): void
    {
        $cycles = ArchitectureScanner::detectCircularModuleDependencies(
            ArchitectureScanner::moduleManifests($this->basePath),
        );

        self::assertSame([], $cycles, implode("\n", $cycles));
    }

    public function test_no_exact_duplicate_php_files_with_same_name(): void
    {
        $duplicates = ArchitectureScanner::scanDuplicateFiles($this->basePath);

        self::assertSame([], $duplicates, implode("\n", $duplicates));
    }
}
