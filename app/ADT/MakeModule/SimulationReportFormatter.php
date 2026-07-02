<?php

declare(strict_types=1);

namespace App\ADT\MakeModule;

/**
 * Renders simulation output for developer review.
 */
final class SimulationReportFormatter
{
    public function format(SimulationReport $report): string
    {
        $lines = [
            'Simulation Report',
            '',
            'Module Name:',
            $report->moduleName,
            '',
            'Directories:',
            '✓ ' . $report->directoryCount,
            '',
            'Files:',
            '✓ ' . $report->fileCount,
            '',
            'Routes:',
            (string) $report->routeCount,
            '',
            'Entities:',
            (string) $report->entityCount,
            '',
            'Controllers:',
            (string) $report->controllerCount,
            '',
            'Estimated Time:',
            $report->estimatedSeconds . ' sec',
            '',
            'Conflicts:',
            $report->conflicts === [] ? 'None' : implode(PHP_EOL, $report->conflicts),
            '',
            'Dependencies:',
            implode(PHP_EOL, $report->dependencies),
            '',
        ];

        if ($report->ready) {
            $lines[] = 'Ready?';
            $lines[] = '';
            $lines[] = '[Y/N]';
        } else {
            $lines[] = 'Generation blocked due to conflicts.';
        }

        return implode(PHP_EOL, $lines);
    }
}
