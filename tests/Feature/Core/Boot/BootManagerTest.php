<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Boot;

use App\Core\Boot\BootFailure;
use App\Core\Boot\BootManager;
use App\Core\Boot\BootReport;
use App\Core\Boot\Support\AbstractModuleBootstrapper;
use App\Core\Boot\Events\BootFailed;
use App\Core\Boot\Events\KernelBooted;
use App\Core\Boot\Events\KernelBooting;
use App\Core\Boot\Events\ModuleBooted;
use App\Core\Boot\Events\ModuleBooting;
use App\Core\Module\Contracts\ProviderChecker;
use App\Core\Module\ModuleLoader;
use App\Core\Module\ModuleManifest;
use App\Core\Module\ModuleRegistry;
use Illuminate\Events\Dispatcher;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Feature coverage for the kernel boot manager: discovery, registration,
 * priority ordering, selective booting, failure tolerance, logging and events.
 */
final class BootManagerTest extends TestCase
{
    private string $modulesPath;

    private ModuleRegistry $registry;

    private RecordingBootstrapper $bootstrapper;

    private RecordingLogger $logger;

    private Dispatcher $events;

    private ProviderChecker $providerChecker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modulesPath = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'axiomos_boot_'
            . uniqid('', true);

        mkdir($this->modulesPath, 0777, true);

        $this->registry = new ModuleRegistry();
        $this->bootstrapper = new RecordingBootstrapper();
        $this->logger = new RecordingLogger();
        $this->events = new Dispatcher();
        $this->providerChecker = new class implements ProviderChecker {
            public function exists(string $providerClass): bool
            {
                return true;
            }
        };
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->modulesPath);

        parent::tearDown();
    }

    public function test_it_boots_enabled_modules_sorted_by_priority(): void
    {
        $this->writeManifest('Zeta', $this->manifest('Zeta', priority: 100));
        $this->writeManifest('Alpha', $this->manifest('Alpha', priority: 1));
        $this->writeManifest('Beta', $this->manifest('Beta', priority: 50));

        $report = $this->manager()->boot();

        self::assertSame(['Alpha', 'Beta', 'Zeta'], $this->bootstrapper->booted);
        self::assertSame(['Alpha', 'Beta', 'Zeta'], $report->loadedModules);
        self::assertSame(3, $report->totalModules);
        self::assertSame([], $report->failedModules);
        self::assertSame([], $report->skippedModules);
    }

    public function test_it_skips_disabled_modules(): void
    {
        $this->writeManifest('On', $this->manifest('On', enabled: true, priority: 1));
        $this->writeManifest('Off', $this->manifest('Off', enabled: false, priority: 2));

        $report = $this->manager()->boot();

        self::assertSame(['On'], $report->loadedModules);
        self::assertSame(['Off'], $report->skippedModules);
        self::assertSame(['On'], $this->bootstrapper->booted);
        self::assertTrue($this->registry->has($this->uuidFor('On')));
        self::assertTrue($this->registry->has($this->uuidFor('Off')));
    }

    public function test_it_continues_booting_when_one_module_fails(): void
    {
        $this->writeManifest('Good', $this->manifest('Good', priority: 1));
        $this->writeManifest('Bad', $this->manifest('Bad', priority: 2));
        $this->writeManifest('AlsoGood', $this->manifest('AlsoGood', priority: 3));

        $this->bootstrapper->failFor('Bad');

        $report = $this->manager()->boot();

        self::assertSame(['Good', 'AlsoGood'], $report->loadedModules);
        self::assertCount(1, $report->failedModules);
        self::assertSame('Bad', $report->failedModules[0]->moduleName);
        self::assertSame('boom', $report->failedModules[0]->message);
        self::assertTrue($report->hasFailures());
        self::assertFalse($report->isSuccessful());
    }

    public function test_it_collects_boot_metrics(): void
    {
        $this->writeManifest('Core', $this->manifest('Core', priority: 1));

        $metrics = $this->manager()->boot()->metrics;

        self::assertSame(1, $metrics->loadedModulesCount);
        self::assertSame(0, $metrics->failedModulesCount);
        self::assertSame(0, $metrics->skippedModulesCount);
        self::assertGreaterThanOrEqual(0.0, $metrics->bootTime);
        self::assertGreaterThan(0, $metrics->memoryAfter);
        self::assertGreaterThanOrEqual($metrics->memoryBefore, $metrics->memoryAfter);
        self::assertGreaterThanOrEqual($metrics->peakMemory, $metrics->memoryAfter);
    }

    public function test_it_registers_all_discovered_modules(): void
    {
        $this->writeManifest('Auth', $this->manifest('Auth', priority: 1));
        $this->writeManifest('Audit', $this->manifest('Audit', priority: 2, enabled: false));

        $this->manager()->boot();

        self::assertNotNull($this->registry->findByName('Auth'));
        self::assertNotNull($this->registry->findByName('Audit'));
    }

    public function test_it_fires_kernel_and_module_lifecycle_events(): void
    {
        $this->writeManifest('Core', $this->manifest('Core', priority: 1));

        $captured = [];
        $this->events->listen('*', static function (string $event) use (&$captured): void {
            $captured[] = $event;
        });

        $this->manager()->boot();

        self::assertContains(KernelBooting::class, $captured);
        self::assertContains(ModuleBooting::class, $captured);
        self::assertContains(ModuleBooted::class, $captured);
        self::assertContains(KernelBooted::class, $captured);
    }

    public function test_it_fires_boot_failed_when_a_module_throws(): void
    {
        $this->writeManifest('Broken', $this->manifest('Broken', priority: 1));
        $this->bootstrapper->failFor('Broken');

        $failures = 0;
        $this->events->listen(BootFailed::class, static function () use (&$failures): void {
            $failures++;
        });

        $this->manager()->boot();

        self::assertSame(1, $failures);
    }

    public function test_it_logs_module_boot_failures(): void
    {
        $this->writeManifest('Broken', $this->manifest('Broken', priority: 1));
        $this->bootstrapper->failFor('Broken');

        $this->manager()->boot();

        self::assertCount(1, $this->logger->errors);
        self::assertStringContainsString('Broken', $this->logger->errors[0]['message']);
        self::assertSame('Broken', $this->logger->errors[0]['context']['module']);
    }

    public function test_it_breaks_priority_ties_alphabetically(): void
    {
        $this->writeManifest('Charlie', $this->manifest('Charlie', priority: 1));
        $this->writeManifest('Alpha', $this->manifest('Alpha', priority: 1));
        $this->writeManifest('Bravo', $this->manifest('Bravo', priority: 1));

        $report = $this->manager()->boot();

        self::assertSame(['Alpha', 'Bravo', 'Charlie'], $report->loadedModules);
    }

    public function test_it_returns_a_boot_report_with_execution_time(): void
    {
        $this->writeManifest('Core', $this->manifest('Core', priority: 1));

        $report = $this->manager()->boot();

        self::assertInstanceOf(BootReport::class, $report);
        self::assertGreaterThanOrEqual(0.0, $report->executionTime);
    }

    public function test_kernel_booted_event_carries_the_final_report(): void
    {
        $this->writeManifest('Core', $this->manifest('Core', priority: 1));

        $received = null;
        $this->events->listen(KernelBooted::class, static function (KernelBooted $event) use (&$received): void {
            $received = $event->report;
        });

        $report = $this->manager()->boot();

        self::assertSame($report, $received);
        self::assertSame(1, $received->totalModules);
    }

    public function test_it_handles_discovery_failure_gracefully(): void
    {
        $this->writeManifest('Broken', '{ invalid json ');

        $failures = 0;
        $this->events->listen(BootFailed::class, static function () use (&$failures): void {
            $failures++;
        });

        $report = $this->manager()->boot();

        self::assertSame(0, $report->totalModules);
        self::assertSame([], $report->loadedModules);
        self::assertCount(1, $report->failedModules);
        self::assertSame('discovery', $report->failedModules[0]->moduleName);
        self::assertSame(1, $failures);
        self::assertCount(1, $this->logger->errors);
        self::assertStringContainsString('discovery', strtolower($this->logger->errors[0]['message']));
    }

    public function test_it_logs_discovery_failures(): void
    {
        $loader = new ModuleLoader(
            $this->modulesPath . DIRECTORY_SEPARATOR . 'does-not-exist',
            $this->providerChecker,
        );

        $manager = new BootManager($loader, $this->registry, $this->bootstrapper, $this->logger, $this->events);
        $manager->boot();

        self::assertCount(1, $this->logger->errors);
        self::assertStringContainsString('discovery', strtolower($this->logger->errors[0]['message']));
    }

    public function test_it_is_idempotent_when_boot_is_called_twice(): void
    {
        $this->writeManifest('Core', $this->manifest('Core', priority: 1));

        $manager = $this->manager();
        $first = $manager->boot();
        $second = $manager->boot();

        self::assertSame(1, $first->totalModules);
        self::assertSame(1, $second->totalModules);
        self::assertSame(2, count($this->bootstrapper->booted));
    }

    public function test_boot_failure_value_object_exposes_module_identity(): void
    {
        $this->writeManifest('Broken', $this->manifest('Broken', priority: 1));
        $this->bootstrapper->failFor('Broken');

        $failure = $this->manager()->boot()->failedModules[0];

        self::assertInstanceOf(BootFailure::class, $failure);
        self::assertSame('Broken', $failure->moduleName);
        self::assertNotEmpty($failure->uuid);
    }

    private function manager(): BootManager
    {
        return new BootManager(
            loader: new ModuleLoader($this->modulesPath, $this->providerChecker),
            registry: $this->registry,
            bootstrapper: $this->bootstrapper,
            logger: $this->logger,
            events: $this->events,
        );
    }

    private function manifest(string $name, int $priority = 1, bool $enabled = true): string
    {
        return json_encode([
            'name' => $name,
            'version' => '1.0.0',
            'minimumCoreVersion' => '1.0.0',
            'provider' => sprintf('Modules\\%s\\Providers\\%sServiceProvider', $name, $name),
            'enabled' => $enabled,
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

    private function uuidFor(string $name): string
    {
        $manifest = $this->registry->findByName($name);

        self::assertNotNull($manifest);

        return $manifest->uuid;
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
    public array $booted = [];

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

        $this->booted[] = $manifest->name;
    }
}

/**
 * @internal test double
 */
final class RecordingLogger extends AbstractLogger implements LoggerInterface
{
    /** @var list<array{level: string, message: string, context: array<string, mixed>}> */
    public array $errors = [];

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if ($level === 'error') {
            $this->errors[] = [
                'level' => (string) $level,
                'message' => (string) $message,
                'context' => $context,
            ];
        }
    }
}
