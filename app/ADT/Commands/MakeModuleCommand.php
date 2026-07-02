<?php

declare(strict_types=1);

namespace App\ADT\Commands;

use App\ADT\Console\ConsoleIO;
use App\ADT\Console\ConsoleIOInterface;
use App\ADT\MakeModule\ModuleBlueprintPlanner;
use App\ADT\MakeModule\ModuleBlueprintWriter;
use App\ADT\MakeModule\ModuleConflictDetector;
use App\ADT\MakeModule\ModuleNameValidator;
use App\ADT\MakeModule\SimulationReport;
use App\ADT\MakeModule\SimulationReportFormatter;
use InvalidArgumentException;

/**
 * Generates a module blueprint with simulation-first developer approval.
 */
final class MakeModuleCommand
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
        try {
            $options = $this->parseOptions($argv);
            $moduleName = (new ModuleNameValidator())->validate($options['name'] ?? '');
            $modulesPath = $this->basePath . DIRECTORY_SEPARATOR . 'modules';

            $planner = new ModuleBlueprintPlanner();
            $artifacts = $planner->plan($moduleName);
            $conflicts = (new ModuleConflictDetector($modulesPath))->detect($moduleName);
            $report = SimulationReport::fromArtifacts($moduleName, $artifacts, $conflicts);

            $this->io->writeln((new SimulationReportFormatter())->format($report));

            if (! $report->ready) {
                return 1;
            }

            if ($options['simulation-only']) {
                $this->io->writeln('Simulation-only mode: no files written.');

                return 0;
            }

            $approved = $options['yes'] || $this->io->askYesNo('Proceed with file generation?');

            if (! $approved) {
                $this->io->writeln('Generation cancelled.');

                return 0;
            }

            $modulePath = (new ModuleBlueprintWriter($modulesPath))->write($moduleName, $artifacts);

            $this->io->writeln('');
            $this->io->writeln("Module blueprint created: {$modulePath}");
            $this->io->writeln('Next steps:');
            $this->io->writeln('- Review README.md, ARCHITECTURE.md, and TODO.md');
            $this->io->writeln('- Run composer dump-autoload');
            $this->io->writeln('- Run composer quality:gate');

            return 0;
        } catch (InvalidArgumentException $exception) {
            $this->io->writeln('Error: ' . $exception->getMessage());

            return 1;
        }
    }

    /**
     * @param list<string> $argv
     *
     * @return array{name: ?string, yes: bool, simulation-only: bool}
     */
    private function parseOptions(array $argv): array
    {
        $options = [
            'name' => null,
            'yes' => false,
            'simulation-only' => false,
        ];

        foreach (array_slice($argv, 1) as $arg) {
            if ($arg === '--yes' || $arg === '-y') {
                $options['yes'] = true;

                continue;
            }

            if ($arg === '--simulation-only' || $arg === '--dry-run') {
                $options['simulation-only'] = true;

                continue;
            }

            if (str_starts_with($arg, '-')) {
                continue;
            }

            if ($options['name'] === null) {
                $options['name'] = $arg;
            }
        }

        return $options;
    }
}
