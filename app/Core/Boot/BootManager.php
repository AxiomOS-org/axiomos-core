<?php

declare(strict_types=1);

namespace App\Core\Boot;

use App\Core\Boot\Contracts\ModuleBootstrapper;
use App\Core\Boot\Events\BootFailed;
use App\Core\Boot\Events\KernelBooted;
use App\Core\Boot\Events\KernelBooting;
use App\Core\Boot\Events\ModuleBooted;
use App\Core\Boot\Events\ModuleBooting;
use App\Core\Module\ModuleDependencyResolver;
use App\Core\Module\ModuleLoader;
use App\Core\Module\ModuleManifest;
use App\Core\Module\ModuleRegistry;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;
use LogicException;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Orchestrates the kernel boot sequence: discover, register, sort and boot modules.
 *
 * Enabled modules are booted in ascending priority order. A single module failure
 * is logged, emitted as {@see BootFailed}, and does not abort the remaining
 * modules. The final {@see BootReport} gives operators and health checks a
 * complete picture of what ran, what was skipped and what broke.
 */
final class BootManager
{
    public function __construct(
        private readonly ModuleLoader $loader,
        private readonly ModuleRegistry $registry,
        private readonly ModuleBootstrapper $bootstrapper,
        private readonly LoggerInterface $logger,
        private readonly ?Dispatcher $events = null,
    ) {
    }

    /**
     * Execute the full kernel boot sequence and return an immutable report.
     */
    public function boot(): BootReport
    {
        $memoryBefore = memory_get_usage(true);
        $startedAt = hrtime(true);

        $this->events?->dispatch(new KernelBooting());

        [$manifests, $discoveryFailure] = $this->discoverModules();

        $sorted = $this->sortModules($manifests);

        $this->registerModules($sorted);

        [$loaded, $failed, $skipped] = $this->bootEnabledModules($sorted);

        if ($discoveryFailure !== null) {
            $failed = [$discoveryFailure, ...$failed];
        }

        return $this->finalize($startedAt, $memoryBefore, $sorted, $loaded, $failed, $skipped);
    }

    /**
     * Discover modules, logging and emitting {@see BootFailed} on failure.
     *
     * @return array{0: Collection<int, ModuleManifest>, 1: BootFailure|null}
     */
    private function discoverModules(): array
    {
        try {
            return [$this->loader->discover(), null];
        } catch (Throwable $exception) {
            $this->recordFailure(null, $exception);

            return [
                new Collection(),
                new BootFailure(
                    moduleName: 'discovery',
                    uuid: '',
                    message: $exception->getMessage(),
                ),
            ];
        }
    }

    /**
     * Sort discovered modules using dependency graph, then priority, then name.
     *
     * @param Collection<int, ModuleManifest> $manifests
     *
     * @return Collection<int, ModuleManifest>
     */
    private function sortModules(Collection $manifests): Collection
    {
        return ModuleDependencyResolver::resolve($manifests);
    }

    /**
     * Register every discovered module into the registry.
     *
     * @param Collection<int, ModuleManifest> $manifests
     */
    private function registerModules(Collection $manifests): void
    {
        foreach ($manifests as $manifest) {
            if ($this->registry->has($manifest->uuid)) {
                continue;
            }

            try {
                $this->registry->register($manifest);
            } catch (LogicException $exception) {
                $this->recordFailure($manifest, $exception);
            }
        }
    }

    /**
     * Boot enabled modules in order, continuing after individual failures.
     *
     * @param Collection<int, ModuleManifest> $manifests
     *
     * @return array{0: list<string>, 1: list<BootFailure>, 2: list<string>}
     */
    private function bootEnabledModules(Collection $manifests): array
    {
        $loaded = [];
        $failed = [];
        $skipped = [];

        foreach ($manifests as $manifest) {
            if (! $manifest->enabled) {
                $skipped[] = $manifest->name;

                continue;
            }

            $this->events?->dispatch(new ModuleBooting($manifest));

            try {
                $this->bootstrapper->boot($manifest);
                $loaded[] = $manifest->name;
                $this->events?->dispatch(new ModuleBooted($manifest));
            } catch (Throwable $exception) {
                $failed[] = new BootFailure(
                    moduleName: $manifest->name,
                    uuid: $manifest->uuid,
                    message: $exception->getMessage(),
                );

                $this->recordFailure($manifest, $exception);
            }
        }

        return [$loaded, $failed, $skipped];
    }

    /**
     * Build the report, emit {@see KernelBooted}, and return.
     *
     * @param list<string>       $loaded
     * @param list<BootFailure>  $failed
     * @param list<string>       $skipped
     */
    private function finalize(
        int $startedAt,
        int $memoryBefore,
        Collection $manifests,
        array $loaded,
        array $failed,
        array $skipped,
    ): BootReport {
        $bootTime = (hrtime(true) - $startedAt) / 1_000_000_000;

        $metrics = new BootMetrics(
            bootTime: $bootTime,
            memoryBefore: $memoryBefore,
            memoryAfter: memory_get_usage(true),
            peakMemory: memory_get_peak_usage(true),
            loadedModulesCount: count($loaded),
            failedModulesCount: count($failed),
            skippedModulesCount: count($skipped),
        );

        $report = new BootReport(
            totalModules: $manifests->count(),
            loadedModules: $loaded,
            failedModules: $failed,
            skippedModules: $skipped,
            executionTime: $bootTime,
            metrics: $metrics,
        );

        $this->events?->dispatch(new KernelBooted($report));

        return $report;
    }

    /**
     * Log a boot failure and emit the {@see BootFailed} event.
     */
    private function recordFailure(?ModuleManifest $manifest, Throwable $exception): void
    {
        $context = [
            'exception' => $exception->getMessage(),
            'exception_class' => $exception::class,
        ];

        if ($manifest !== null) {
            $context['module'] = $manifest->name;
            $context['uuid'] = $manifest->uuid;
            $context['provider'] = $manifest->provider;

            $this->logger->error(
                sprintf('Module "%s" failed to boot.', $manifest->name),
                $context,
            );
        } else {
            $this->logger->error('Module discovery failed.', $context);
        }

        $this->events?->dispatch(new BootFailed($manifest, $exception));
    }
}
