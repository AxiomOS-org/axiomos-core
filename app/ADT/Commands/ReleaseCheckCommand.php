<?php

declare(strict_types=1);

namespace App\ADT\Commands;

use App\ADT\Console\ConsoleIO;
use App\ADT\Console\ConsoleIOInterface;
use App\ADT\Release\ReleaseManager;

final class ReleaseCheckCommand
{
    public function __construct(
        private readonly string $basePath,
        private readonly ConsoleIOInterface $io = new ConsoleIO(),
    ) {
    }

    /**
     * @param list<string> $argv
     */
    public function run(array $argv): int
    {
        $manager = new ReleaseManager($this->basePath, '1.0.0');
        $report = $manager->readiness();

        $this->io->writeln('Release Readiness Report');
        $this->io->writeln('Version: ' . $report->version);
        $this->io->writeln('Ready: ' . ($report->ready ? 'YES' : 'NO'));
        $this->io->writeln('');
        $this->io->writeln('Checks:');

        foreach ($report->checks as $check) {
            $this->io->writeln('  ✓ ' . $check);
        }

        if ($report->failures !== []) {
            $this->io->writeln('');
            $this->io->writeln('Failures:');

            foreach ($report->failures as $failure) {
                $this->io->writeln('  ✗ ' . $failure);
            }
        }

        return $report->ready ? 0 : 1;
    }
}
