<?php

declare(strict_types=1);

namespace App\Core\Boot;

/**
 * Runtime metrics captured during a single boot sequence.
 *
 * Designed for export to Prometheus, OpenTelemetry and internal health checks.
 * All memory values are bytes as reported by PHP.
 */
final readonly class BootMetrics
{
    /**
     * @param float $bootTime            Wall-clock seconds for the full boot sequence.
     * @param int   $memoryBefore        Resident memory before boot began.
     * @param int   $memoryAfter         Resident memory after boot completed.
     * @param int   $peakMemory          Peak memory during boot.
     * @param int   $loadedModulesCount  Enabled modules that booted successfully.
     * @param int   $failedModulesCount  Modules that failed during boot or discovery.
     * @param int   $skippedModulesCount Disabled modules that were not booted.
     */
    public function __construct(
        public float $bootTime,
        public int $memoryBefore,
        public int $memoryAfter,
        public int $peakMemory,
        public int $loadedModulesCount,
        public int $failedModulesCount,
        public int $skippedModulesCount,
    ) {
    }

    /**
     * @return array<string, int|float>
     */
    public function toArray(): array
    {
        return [
            'boot_time' => $this->bootTime,
            'memory_before' => $this->memoryBefore,
            'memory_after' => $this->memoryAfter,
            'peak_memory' => $this->peakMemory,
            'loaded_modules' => $this->loadedModulesCount,
            'failed_modules' => $this->failedModulesCount,
            'skipped_modules' => $this->skippedModulesCount,
        ];
    }
}
