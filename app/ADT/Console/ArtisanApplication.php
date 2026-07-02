<?php

declare(strict_types=1);

namespace App\ADT\Console;

use App\ADT\Commands\MakeModuleCommand;
use App\ADT\Commands\ReleaseCheckCommand;

/**
 * Lightweight artisan-style CLI router for AxiomOS ADT commands.
 */
final class ArtisanApplication
{
    public function __construct(
        private readonly string $basePath,
    ) {
    }

    /**
     * @param list<string> $argv
     */
    public function run(array $argv): int
    {
        $command = $argv[1] ?? null;

        if ($command === null || $command === '--help' || $command === '-h') {
            $this->printHelp();

            return 0;
        }

        if ($command === 'axiomos:make-module') {
            return (new MakeModuleCommand($this->basePath))->run(array_slice($argv, 1));
        }

        if ($command === 'axiomos:release-check') {
            return (new ReleaseCheckCommand($this->basePath))->run(array_slice($argv, 1));
        }

        fwrite(STDERR, "Unknown command: {$command}" . PHP_EOL);
        $this->printHelp();

        return 1;
    }

    private function printHelp(): void
    {
        $help = <<<HELP
AxiomOS ADT

Usage:
  php artisan axiomos:make-module <ModuleName> [--yes] [--simulation-only]
  php artisan axiomos:release-check

Options:
  --yes, -y            Approve generation without interactive prompt
  --simulation-only    Run simulation report only (no file writes)
  --dry-run            Alias of --simulation-only

HELP;

        fwrite(STDOUT, $help);
    }
}
