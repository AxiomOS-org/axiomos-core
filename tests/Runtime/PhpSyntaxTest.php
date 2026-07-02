<?php

declare(strict_types=1);

namespace Tests\Runtime;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Tests\Support\PostgresFeatureTestCase;

final class PhpSyntaxTest extends PostgresFeatureTestCase
{
    public function test_application_php_files_have_no_parse_errors(): void
    {
        $failures = [];

        foreach (['app', 'modules', 'tests'] as $directory) {
            $path = $this->basePath . DIRECTORY_SEPARATOR . $directory;

            if (! is_dir($path)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            );

            /** @var SplFileInfo $file */
            foreach ($iterator as $file) {
                if (! $file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $output = [];
                $exitCode = 0;
                exec('php -l ' . escapeshellarg($file->getPathname()) . ' 2>&1', $output, $exitCode);

                if ($exitCode !== 0) {
                    $failures[] = $file->getPathname() . ': ' . implode(' ', $output);
                }
            }
        }

        self::assertSame([], $failures, "Parse errors detected:\n" . implode("\n", $failures));
    }
}
