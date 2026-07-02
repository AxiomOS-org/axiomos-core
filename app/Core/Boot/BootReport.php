<?php

declare(strict_types=1);

namespace App\Core\Boot;

use JsonException;

/**
 * Rich boot summary with computed rates and serialisation helpers.
 *
 * Not a bare DTO: carries {@see BootMetrics} and exposes query methods for
 * health checks, observability exporters and operator dashboards.
 */
final readonly class BootReport
{
    /**
     * @param int                    $totalModules   Every module discovered on disk.
     * @param list<string>           $loadedModules  Enabled modules that booted successfully.
     * @param list<BootFailure>      $failedModules  Modules that threw during boot or discovery.
     * @param list<string>           $skippedModules Disabled modules that were not booted.
     * @param float                  $executionTime  Wall-clock seconds for the full boot sequence.
     * @param BootMetrics            $metrics        Runtime metrics captured during boot.
     */
    public function __construct(
        public int $totalModules,
        public array $loadedModules,
        public array $failedModules,
        public array $skippedModules,
        public float $executionTime,
        public BootMetrics $metrics,
    ) {
    }

    public function isSuccessful(): bool
    {
        return ! $this->hasFailures();
    }

    public function hasFailures(): bool
    {
        return $this->failedModules !== [];
    }

    public function successRate(): float
    {
        $attempted = count($this->loadedModules) + count($this->failedModules);

        if ($attempted === 0) {
            return 1.0;
        }

        return count($this->loadedModules) / $attempted;
    }

    public function failureRate(): float
    {
        return 1.0 - $this->successRate();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'total_modules' => $this->totalModules,
            'loaded_modules' => $this->loadedModules,
            'failed_modules' => array_map(
                static fn (BootFailure $failure): array => $failure->toArray(),
                $this->failedModules,
            ),
            'skipped_modules' => $this->skippedModules,
            'execution_time' => $this->executionTime,
            'is_successful' => $this->isSuccessful(),
            'success_rate' => $this->successRate(),
            'failure_rate' => $this->failureRate(),
            'metrics' => $this->metrics->toArray(),
        ];
    }

    /**
     * @throws JsonException
     */
    public function toJson(int $flags = JSON_THROW_ON_ERROR): string
    {
        return json_encode($this->toArray(), $flags);
    }
}
