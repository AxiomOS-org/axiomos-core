<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Boot;

use App\Core\Boot\BootFailure;
use App\Core\Boot\BootMetrics;
use App\Core\Boot\BootReport;
use PHPUnit\Framework\TestCase;

final class BootReportTest extends TestCase
{
    public function test_it_reports_success_and_failure_rates(): void
    {
        $report = $this->report(
            loaded: ['Alpha', 'Beta'],
            failed: [new BootFailure('Gamma', 'uuid', 'error')],
        );

        self::assertTrue($report->isSuccessful() === false);
        self::assertTrue($report->hasFailures());
        self::assertEqualsWithDelta(2 / 3, $report->successRate(), 0.0001);
        self::assertEqualsWithDelta(1 / 3, $report->failureRate(), 0.0001);
    }

    public function test_it_reports_full_success_when_nothing_failed(): void
    {
        $report = $this->report(loaded: ['Alpha']);

        self::assertTrue($report->isSuccessful());
        self::assertFalse($report->hasFailures());
        self::assertSame(1.0, $report->successRate());
        self::assertSame(0.0, $report->failureRate());
    }

    public function test_it_serialises_to_array_and_json(): void
    {
        $report = $this->report(loaded: ['Alpha']);

        $array = $report->toArray();
        $json = $report->toJson();

        self::assertTrue($array['is_successful']);
        self::assertArrayHasKey('metrics', $array);
        self::assertStringContainsString('"loaded_modules"', $json);
        self::assertJson($json);
    }

    /**
     * @param list<string>      $loaded
     * @param list<BootFailure> $failed
     * @param list<string>      $skipped
     */
    private function report(
        array $loaded = [],
        array $failed = [],
        array $skipped = [],
    ): BootReport {
        return new BootReport(
            totalModules: count($loaded) + count($failed) + count($skipped),
            loadedModules: $loaded,
            failedModules: $failed,
            skippedModules: $skipped,
            executionTime: 0.125,
            metrics: new BootMetrics(
                bootTime: 0.125,
                memoryBefore: 1_000_000,
                memoryAfter: 2_000_000,
                peakMemory: 2_500_000,
                loadedModulesCount: count($loaded),
                failedModulesCount: count($failed),
                skippedModulesCount: count($skipped),
            ),
        );
    }
}
