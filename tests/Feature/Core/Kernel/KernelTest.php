<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Kernel;

use App\Core\Boot\BootManager;
use App\Core\Boot\Support\AbstractModuleBootstrapper;
use App\Core\Configuration\ConfigurationBuilder;
use App\Core\Configuration\ConfigurationManager;
use App\Core\Container\Container;
use App\Core\Kernel\Events\KernelInitialized;
use App\Core\Kernel\Events\KernelInitializing;
use App\Core\Kernel\Events\KernelReady;
use App\Core\Kernel\Events\KernelShutdown;
use App\Core\Kernel\Kernel;
use App\Core\Kernel\KernelManager;
use App\Core\Kernel\KernelState;
use App\Core\Module\Contracts\ProviderChecker;
use App\Core\Module\ModuleLoader;
use App\Core\Module\ModuleManifest;
use App\Core\Module\ModuleRegistry;
use Illuminate\Events\Dispatcher;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use RuntimeException;

final class KernelTest extends TestCase
{
    private string $modulesPath;

    private ModuleRegistry $registry;

    private RecordingBootstrapper $bootstrapper;

    private Dispatcher $events;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modulesPath = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'axiomos_kernel_'
            . uniqid('', true);

        mkdir($this->modulesPath, 0777, true);

        $this->registry = new ModuleRegistry();
        $this->bootstrapper = new RecordingBootstrapper();
        $this->events = new Dispatcher();
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->modulesPath);

        parent::tearDown();
    }

    public function test_it_boots_through_the_full_lifecycle(): void
    {
        $this->writeManifest('Core', $this->manifest('Core'));

        $kernel = $this->kernel();
        $report = $kernel->boot();

        self::assertSame(KernelState::Ready, $kernel->state());
        self::assertSame(['Core'], $report->loadedModules);
        self::assertTrue($kernel->container()->has(\App\Core\Kernel\Contracts\KernelInterface::class));
    }

    public function test_kernel_manager_exposes_kernel_operations(): void
    {
        $this->writeManifest('Core', $this->manifest('Core'));

        $manager = new KernelManager($this->kernel());
        $report = $manager->boot();

        self::assertSame(['Core'], $report->loadedModules);
        self::assertSame('ready', $manager->status()['status']);
        self::assertSame('AxiomOS', $manager->status()['kernel']);
        self::assertTrue($manager->health()['healthy']);
        self::assertSame(1, $manager->metrics()['bootCount']);
    }

    public function test_it_fires_kernel_lifecycle_events(): void
    {
        $this->writeManifest('Core', $this->manifest('Core'));

        $captured = [];
        $this->events->listen('*', static function (string $event) use (&$captured): void {
            $captured[] = $event;
        });

        $this->kernel()->boot();

        self::assertContains(KernelInitializing::class, $captured);
        self::assertContains(KernelInitialized::class, $captured);
        self::assertContains(KernelReady::class, $captured);
    }

    public function test_it_shuts_down_and_reloads(): void
    {
        $this->writeManifest('Core', $this->manifest('Core'));

        $kernel = $this->kernel();
        $kernel->boot();
        $kernel->shutdown();

        self::assertSame(KernelState::Shutdown, $kernel->state());

        $kernel->reload();

        self::assertSame(KernelState::Ready, $kernel->state());
        self::assertSame(2, $kernel->metrics()['bootCount']);
    }

    public function test_manager_reload_delegates_to_kernel(): void
    {
        $this->writeManifest('Core', $this->manifest('Core'));

        $manager = new KernelManager($this->kernel());
        $manager->boot();
        $manager->shutdown();
        $manager->reload();

        self::assertSame('ready', $manager->status()['status']);
    }

    public function test_health_returns_required_fields(): void
    {
        $this->writeManifest('Core', $this->manifest('Core'));

        $kernel = $this->kernel();
        $kernel->boot();
        $health = $kernel->health();

        self::assertIsArray($health);
        self::assertArrayHasKey('kernelVersion', $health);
        self::assertArrayHasKey('uptime', $health);
        self::assertArrayHasKey('modulesLoaded', $health);
        self::assertArrayHasKey('modulesFailed', $health);
        self::assertArrayHasKey('memoryUsage', $health);
        self::assertArrayHasKey('bootDuration', $health);
        self::assertArrayHasKey('environment', $health);
    }

    public function test_metrics_returns_required_fields(): void
    {
        $this->writeManifest('Core', $this->manifest('Core'));

        $this->kernel()->boot();
        $metrics = $this->kernel()->metrics();

        self::assertArrayHasKey('bootCount', $metrics);
        self::assertArrayHasKey('averageBootTime', $metrics);
        self::assertArrayHasKey('lastBootTime', $metrics);
        self::assertArrayHasKey('loadedModules', $metrics);
        self::assertArrayHasKey('failedModules', $metrics);
        self::assertArrayHasKey('totalMemory', $metrics);
        self::assertArrayHasKey('bootMetrics', $metrics);
    }

    public function test_status_matches_target_response_shape(): void
    {
        $this->writeManifest('Core', $this->manifest('Core'));
        $this->writeManifest('Auth', $this->manifest('Auth', priority: 2));

        $manager = new KernelManager($this->kernel());
        $manager->boot();
        $status = $manager->status();

        self::assertSame('AxiomOS', $status['kernel']);
        self::assertSame('ready', $status['status']);
        self::assertSame(2, $status['modules']);
        self::assertStringContainsString('ms', $status['bootTime']);
    }

    public function test_it_marks_health_unhealthy_when_module_boot_fails(): void
    {
        $this->writeManifest('Broken', $this->manifest('Broken'));
        $this->bootstrapper->failFor('Broken');

        $kernel = $this->kernel();
        $kernel->boot();

        self::assertFalse($kernel->health()['healthy']);
        self::assertSame(1, $kernel->health()['modulesFailed']);
    }

    public function test_it_prevents_double_boot_without_reload(): void
    {
        $this->writeManifest('Core', $this->manifest('Core'));

        $kernel = $this->kernel();
        $kernel->boot();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('already booted');

        $kernel->boot();
    }

    public function test_shutdown_is_idempotent(): void
    {
        $this->writeManifest('Core', $this->manifest('Core'));

        $kernel = $this->kernel();
        $kernel->boot();
        $kernel->shutdown();
        $kernel->shutdown();

        self::assertSame(KernelState::Shutdown, $kernel->state());
    }

    public function test_configuration_is_loaded_during_initialize(): void
    {
        $this->writeManifest('Core', $this->manifest('Core'));

        $configuration = ConfigurationBuilder::create(dirname(__DIR__, 4))->build();
        $kernel = $this->kernel(configuration: $configuration);
        $kernel->boot();

        self::assertSame('AxiomOS', $configuration->get('app.name'));
    }

    private function kernel(?ConfigurationManager $configuration = null): Kernel
    {
        $configuration ??= ConfigurationBuilder::create(dirname(__DIR__, 4))->build();
        $providerChecker = new class implements ProviderChecker {
            public function exists(string $providerClass): bool
            {
                return true;
            }
        };

        $bootManager = new BootManager(
            loader: new ModuleLoader($this->modulesPath, $providerChecker),
            registry: $this->registry,
            bootstrapper: $this->bootstrapper,
            logger: new NullLogger(),
            events: $this->events,
        );

        return new Kernel(
            bootManager: $bootManager,
            moduleRegistry: $this->registry,
            container: new Container(),
            configuration: $configuration,
            events: $this->events,
            version: '1.0.0',
            environment: 'testing',
        );
    }

    private function manifest(string $name, int $priority = 1): string
    {
        return json_encode([
            'name' => $name,
            'version' => '1.0.0',
            'minimumCoreVersion' => '1.0.0',
            'provider' => sprintf('Modules\\%s\\Providers\\%sServiceProvider', $name, $name),
            'enabled' => true,
            'priority' => $priority,
            'dependencies' => [],
            'authors' => [['name' => 'AxiomOS Team']],
        ], JSON_THROW_ON_ERROR);
    }

    private function writeManifest(string $folder, string $json): void
    {
        $directory = $this->modulesPath . DIRECTORY_SEPARATOR . $folder;
        mkdir($directory, 0777, true);
        file_put_contents($directory . DIRECTORY_SEPARATOR . 'module.json', $json);
    }

    private function removeDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        foreach (scandir($path) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $target = $path . DIRECTORY_SEPARATOR . $entry;

            is_dir($target) ? $this->removeDirectory($target) : unlink($target);
        }

        rmdir($path);
    }
}

/**
 * @internal test double
 */
final class RecordingBootstrapper extends AbstractModuleBootstrapper
{
    /** @var list<string> */
    private array $failures = [];

    public function failFor(string $moduleName): void
    {
        $this->failures[] = $moduleName;
    }

    public function boot(ModuleManifest $manifest): void
    {
        if (in_array($manifest->name, $this->failures, true)) {
            throw new RuntimeException('boom');
        }
    }
}
