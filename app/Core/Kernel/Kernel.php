<?php

declare(strict_types=1);

namespace App\Core\Kernel;

use App\Core\Boot\BootManager;
use App\Core\Boot\BootReport;
use App\Core\Configuration\Contracts\ConfigurationManager;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Kernel\Contracts\KernelInterface;
use App\Core\Kernel\Events\KernelInitialized;
use App\Core\Kernel\Events\KernelInitializing;
use App\Core\Kernel\Events\KernelReady;
use App\Core\Kernel\Events\KernelShutdown;
use App\Core\Module\ModuleRegistry;
use Illuminate\Contracts\Events\Dispatcher;
use LogicException;

/**
 * AxiomOS kernel — orchestrates configuration, the service container, module
 * discovery and boot into a single production-ready lifecycle.
 *
 * Designed for long-running runtimes (RoadRunner, Octane, Swoole) and
 * Kubernetes health probes. Exposes granular lifecycle phases plus aggregated
 * status, health and metrics for observability pipelines.
 */
final class Kernel implements KernelInterface
{
    private KernelState $state = KernelState::Cold;

    private ?float $startedAt = null;

    private ?BootReport $lastBootReport = null;

    /** @var list<float> */
    private array $bootTimes = [];

    public function __construct(
        private readonly BootManager $bootManager,
        private readonly ModuleRegistry $moduleRegistry,
        private readonly ContainerInterface $container,
        private readonly ConfigurationManager $configuration,
        private readonly ?Dispatcher $events = null,
        private readonly string $version = '1.0.0',
        private readonly string $environment = 'local',
    ) {
    }

    public function initialize(): void
    {
        $this->assertState(KernelState::Cold, KernelState::Shutdown);

        $this->state = KernelState::Initializing;
        $this->events?->dispatch(new KernelInitializing());

        $this->configuration->load();

        if ($this->startedAt === null) {
            $this->startedAt = microtime(true);
        }

        $this->state = KernelState::Initialized;
        $this->events?->dispatch(new KernelInitialized());
    }

    public function register(): void
    {
        $this->assertState(KernelState::Initialized);

        $this->state = KernelState::Registering;

        $this->container->instance(KernelInterface::class, $this);
        $this->container->instance(ModuleRegistry::class, $this->moduleRegistry);
        $this->container->instance(ConfigurationManager::class, $this->configuration);

        $this->state = KernelState::Registered;
    }

    public function boot(): BootReport
    {
        if ($this->state === KernelState::Ready) {
            throw new LogicException('Kernel is already booted. Use reload() to reboot.');
        }

        if ($this->state === KernelState::Cold || $this->state === KernelState::Shutdown) {
            $this->initialize();
        }

        if ($this->state === KernelState::Initialized) {
            $this->register();
        }

        $this->assertState(KernelState::Registered);

        $this->state = KernelState::Booting;

        $report = $this->bootManager->boot();
        $this->lastBootReport = $report;
        $this->bootTimes[] = $report->executionTime;

        $this->ready($report);

        return $report;
    }

    public function ready(BootReport $report): void
    {
        $this->state = KernelState::Ready;
        $this->events?->dispatch(new KernelReady($report));
    }

    public function shutdown(): void
    {
        if ($this->state === KernelState::Cold || $this->state === KernelState::Shutdown) {
            return;
        }

        $this->state = KernelState::ShuttingDown;
        $this->events?->dispatch(new KernelShutdown());
        $this->state = KernelState::Shutdown;
    }

    public function reload(): BootReport
    {
        $this->shutdown();
        $this->state = KernelState::Cold;

        return $this->boot();
    }

    /**
     * @return array<string, mixed>
     */
    public function status(): array
    {
        $report = $this->lastBootReport;

        return [
            'kernel' => 'AxiomOS',
            'status' => $this->state->value,
            'version' => $this->version,
            'environment' => $this->environment,
            'uptime' => $this->uptime(),
            'modules' => $report?->metrics->loadedModulesCount ?? 0,
            'bootTime' => sprintf('%.2f ms', ($report?->executionTime ?? 0.0) * 1_000),
            'modules_registered' => $this->moduleRegistry->all()->count(),
            'modules_enabled' => $this->moduleRegistry->enabled()->count(),
            'ready' => $this->state === KernelState::Ready,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function health(): array
    {
        $report = $this->lastBootReport;

        return [
            'kernelVersion' => $this->version,
            'uptime' => $this->uptime(),
            'modulesLoaded' => $report?->metrics->loadedModulesCount ?? 0,
            'modulesFailed' => $report?->metrics->failedModulesCount ?? 0,
            'memoryUsage' => memory_get_usage(true),
            'bootDuration' => $report?->executionTime ?? 0.0,
            'environment' => $this->environment,
            'healthy' => $this->state === KernelState::Ready && ($report?->isSuccessful() ?? false),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function metrics(): array
    {
        $report = $this->lastBootReport;

        return [
            'bootCount' => count($this->bootTimes),
            'averageBootTime' => $this->averageBootTime(),
            'lastBootTime' => $report?->executionTime ?? 0.0,
            'loadedModules' => $report?->loadedModules ?? [],
            'failedModules' => array_map(
                static fn ($failure): array => $failure->toArray(),
                $report?->failedModules ?? [],
            ),
            'totalMemory' => memory_get_usage(true),
            'peakMemory' => memory_get_peak_usage(true),
            'bootMetrics' => $report?->metrics->toArray(),
        ];
    }

    public function state(): KernelState
    {
        return $this->state;
    }

    public function container(): ContainerInterface
    {
        return $this->container;
    }

    public function registry(): ModuleRegistry
    {
        return $this->moduleRegistry;
    }

    public function lastBootReport(): ?BootReport
    {
        return $this->lastBootReport;
    }

    private function uptime(): float
    {
        if ($this->startedAt === null) {
            return 0.0;
        }

        return microtime(true) - $this->startedAt;
    }

    private function averageBootTime(): float
    {
        if ($this->bootTimes === []) {
            return 0.0;
        }

        return array_sum($this->bootTimes) / count($this->bootTimes);
    }

    private function assertState(KernelState ...$allowed): void
    {
        foreach ($allowed as $state) {
            if ($this->state === $state) {
                return;
            }
        }

        throw new LogicException(sprintf(
            'Invalid kernel state transition from "%s".',
            $this->state->value,
        ));
    }
}
