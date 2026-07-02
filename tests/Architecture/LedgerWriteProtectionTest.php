<?php

declare(strict_types=1);

namespace Tests\Architecture;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Tests\Support\PostgresFeatureTestCase;

/**
 * Enforces ACC-BP-1.0: no ERP module may write to accounting ledger tables directly.
 */
final class LedgerWriteProtectionTest extends PostgresFeatureTestCase
{
    /**
     * @var list<string>
     */
    private const PROTECTED_TABLE_FRAGMENTS = [
        'accounting_journal_lines',
        'accounting_journals',
        'accounting_ledger_balances',
        'accounting_general_ledger',
        'accounting_posting_log',
    ];

    /**
     * @var list<string>
     */
    private const WRITE_PATTERNS = [
        '/->insert\s*\(/i',
        '/->update\s*\(/i',
        '/::query\s*\(\s*\)\s*->\s*create\s*\(/i',
        '/::create\s*\(/i',
        '/DB::table\s*\(/i',
    ];

    public function test_non_accounting_modules_do_not_write_ledger_tables(): void
    {
        $violations = [];
        $modulesPath = $this->basePath . DIRECTORY_SEPARATOR . 'modules';

        foreach (glob($modulesPath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [] as $moduleDirectory) {
            $moduleName = basename($moduleDirectory);

            if (strcasecmp($moduleName, 'Accounting') === 0) {
                continue;
            }

            foreach ($this->phpFiles($moduleDirectory) as $file) {
                if (str_contains($file, DIRECTORY_SEPARATOR . 'Infrastructure' . DIRECTORY_SEPARATOR)) {
                    continue;
                }

                $content = (string) file_get_contents($file);

                foreach (self::PROTECTED_TABLE_FRAGMENTS as $table) {
                    if (! str_contains($content, $table)) {
                        continue;
                    }

                    foreach (self::WRITE_PATTERNS as $pattern) {
                        if (preg_match($pattern, $content) === 1) {
                            $violations[] = sprintf(
                                '%s references %s with a write pattern',
                                $this->relative($file),
                                $table,
                            );
                        }
                    }
                }
            }
        }

        self::assertSame([], $violations, implode("\n", $violations));
    }

    /**
     * @return list<string>
     */
    private function phpFiles(string $directory): array
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

    private function relative(string $file): string
    {
        return str_replace($this->basePath . DIRECTORY_SEPARATOR, '', $file);
    }
}
