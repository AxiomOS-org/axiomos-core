<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Module;

use App\Core\Module\Contracts\ProviderChecker;
use App\Core\Module\Events\ModuleDiscovered;
use App\Core\Module\Events\ModuleFailed;
use App\Core\Module\Events\ModuleLoaded;
use App\Core\Module\Exceptions\InvalidModuleException;
use App\Core\Module\ModuleLoader;
use App\Core\Module\ModuleManifest;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

/**
 * Feature coverage for the module discovery pipeline: scanning, validation,
 * UUID identity, deterministic caching and lifecycle events.
 *
 * Each test builds a throw-away modules directory on disk so behaviour is
 * exercised end-to-end without coupling to the real, evolving `/modules` tree.
 */
final class ModuleDiscoveryTest extends TestCase
{
    private string $modulesPath;

    private ProviderChecker $providerChecker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modulesPath = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'axiomos_modules_'
            . uniqid('', true);

        mkdir($this->modulesPath, 0777, true);

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

    public function test_it_discovers_every_valid_module(): void
    {
        $this->writeManifest('Authentication', $this->validManifest('Authentication', priority: 1));
        $this->writeManifest('Authorization', $this->validManifest('Authorization', priority: 2));

        $manifests = $this->loader()->discover();

        self::assertCount(2, $manifests);
        self::assertEqualsCanonicalizing(
            ['Authentication', 'Authorization'],
            $manifests->map(static fn (ModuleManifest $m): string => $m->name)->all(),
        );
    }

    public function test_it_returns_a_collection_of_manifests(): void
    {
        $this->writeManifest('Core', $this->validManifest('Core'));

        $manifests = $this->loader()->discover();

        self::assertInstanceOf(Collection::class, $manifests);
        self::assertContainsOnlyInstancesOf(ModuleManifest::class, $manifests);
    }

    public function test_it_hydrates_manifest_fields(): void
    {
        $this->writeManifest('Audit', $this->validManifest('Audit', priority: 5));

        $manifest = $this->loader()->discover()->firstOrFail();

        self::assertSame('Audit', $manifest->id);
        self::assertSame('Audit', $manifest->name);
        self::assertSame('1.0.0', $manifest->version);
        self::assertSame('Modules\\Audit\\Providers\\AuditServiceProvider', $manifest->provider);
        self::assertTrue($manifest->enabled);
        self::assertSame(5, $manifest->priority);
        self::assertSame([], $manifest->dependencies);
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            $manifest->uuid,
        );
    }

    public function test_it_derives_a_stable_uuid_for_manifests_without_one(): void
    {
        $this->writeManifest('Settings', $this->validManifest('Settings'));

        $first = $this->loader()->discover()->firstOrFail()->uuid;
        $second = $this->loader()->discover()->firstOrFail()->uuid;

        self::assertSame($first, $second);
    }

    public function test_it_honours_an_explicitly_declared_uuid(): void
    {
        $uuid = '11111111-2222-3333-4444-555555555555';
        $this->writeManifest('Plugin', $this->validManifest('Plugin', uuid: $uuid));

        $manifest = $this->loader()->discover()->firstOrFail();

        self::assertSame($uuid, $manifest->uuid);
    }

    public function test_it_includes_disabled_modules(): void
    {
        $this->writeManifest('Workflow', $this->validManifest('Workflow', enabled: false));

        self::assertFalse($this->loader()->discover()->firstOrFail()->enabled);
    }

    public function test_it_ignores_folders_without_a_manifest(): void
    {
        $this->writeManifest('Events', $this->validManifest('Events'));
        mkdir($this->modulesPath . DIRECTORY_SEPARATOR . 'NotAModule', 0777, true);

        self::assertCount(1, $this->loader()->discover());
    }

    public function test_it_ignores_loose_files_in_the_modules_path(): void
    {
        $this->writeManifest('Settings', $this->validManifest('Settings'));
        file_put_contents($this->modulesPath . DIRECTORY_SEPARATOR . 'readme.md', '# not a module');

        self::assertCount(1, $this->loader()->discover());
    }

    public function test_it_caches_discovered_modules(): void
    {
        $cache = $this->cache();
        $this->writeManifest('Api', $this->validManifest('Api'));

        $loader = $this->loader(cache: $cache);
        self::assertCount(1, $loader->discover());

        $this->removeDirectory($this->modulesPath . DIRECTORY_SEPARATOR . 'Api');

        self::assertCount(1, $loader->discover(), 'Second call must be served from cache.');
    }

    public function test_it_rescans_after_the_cache_is_flushed(): void
    {
        $cache = $this->cache();
        $this->writeManifest('Api', $this->validManifest('Api'));

        $loader = $this->loader(cache: $cache);
        $loader->discover();
        $loader->flush();

        $this->removeDirectory($this->modulesPath . DIRECTORY_SEPARATOR . 'Api');

        self::assertCount(0, $loader->discover());
    }

    public function test_it_emits_lifecycle_events(): void
    {
        $events = new Dispatcher();
        $captured = [];
        $events->listen('*', static function (string $event) use (&$captured): void {
            $captured[] = $event;
        });

        $this->writeManifest('Notifications', $this->validManifest('Notifications'));
        $this->loader(events: $events)->discover();

        self::assertContains(ModuleDiscovered::class, $captured);
        self::assertContains(ModuleLoaded::class, $captured);
    }

    public function test_it_emits_a_failure_event_before_rethrowing(): void
    {
        $events = new Dispatcher();
        $failed = 0;
        $events->listen(ModuleFailed::class, static function () use (&$failed): void {
            $failed++;
        });

        $this->writeManifest('Broken', '{ "name": "Broken", ');

        try {
            $this->loader(events: $events)->discover();
            self::fail('Expected InvalidModuleException.');
        } catch (InvalidModuleException) {
            self::assertSame(1, $failed);
        }
    }

    public function test_it_throws_when_the_modules_path_is_missing(): void
    {
        $loader = new ModuleLoader(
            $this->modulesPath . DIRECTORY_SEPARATOR . 'nope',
            $this->providerChecker,
        );

        $this->expectException(InvalidModuleException::class);
        $this->expectExceptionMessage('does not exist or is not a directory');

        $loader->discover();
    }

    public function test_it_throws_on_malformed_json(): void
    {
        $this->writeManifest('Broken', '{ "name": "Broken", ');

        $this->expectException(InvalidModuleException::class);
        $this->expectExceptionMessage('invalid JSON');

        $this->loader()->discover();
    }

    public function test_it_throws_when_a_required_field_is_missing(): void
    {
        $this->writeManifest('NoProvider', json_encode([
            'name' => 'NoProvider',
            'version' => '1.0.0',
            'minimumCoreVersion' => '1.0.0',
            'enabled' => true,
            'dependencies' => [],
        ], JSON_THROW_ON_ERROR));

        $this->expectException(InvalidModuleException::class);
        $this->expectExceptionMessage('missing the required field "provider"');

        $this->loader()->discover();
    }

    public function test_it_throws_when_enabled_is_not_a_boolean(): void
    {
        $this->writeManifest('BadEnabled', json_encode([
            'name' => 'BadEnabled',
            'version' => '1.0.0',
            'minimumCoreVersion' => '1.0.0',
            'provider' => 'Modules\\BadEnabled\\Providers\\BadEnabledServiceProvider',
            'enabled' => 'yes',
            'dependencies' => [],
        ], JSON_THROW_ON_ERROR));

        $this->expectException(InvalidModuleException::class);
        $this->expectExceptionMessage('field "enabled" must be of type boolean');

        $this->loader()->discover();
    }

    public function test_it_throws_on_a_non_semantic_version(): void
    {
        $this->writeManifest('BadVersion', json_encode([
            'name' => 'BadVersion',
            'version' => 'v1',
            'minimumCoreVersion' => '1.0.0',
            'provider' => 'Modules\\BadVersion\\Providers\\BadVersionServiceProvider',
            'enabled' => true,
            'dependencies' => [],
        ], JSON_THROW_ON_ERROR));

        $this->expectException(InvalidModuleException::class);
        $this->expectExceptionMessage('not valid semantic versioning');

        $this->loader()->discover();
    }

    public function test_it_throws_when_the_provider_class_does_not_exist(): void
    {
        $this->writeManifest('Ghost', $this->validManifest('Ghost'));

        $checker = new class implements ProviderChecker {
            public function exists(string $providerClass): bool
            {
                return false;
            }
        };

        $loader = new ModuleLoader($this->modulesPath, $checker);

        $this->expectException(InvalidModuleException::class);
        $this->expectExceptionMessage('does not exist');

        $loader->discover();
    }

    public function test_it_throws_when_the_core_version_is_too_old(): void
    {
        $this->writeManifest('Future', json_encode([
            'name' => 'Future',
            'version' => '1.0.0',
            'provider' => 'Modules\\Future\\Providers\\FutureServiceProvider',
            'enabled' => true,
            'minimumCoreVersion' => '2.0.0',
            'dependencies' => [],
        ], JSON_THROW_ON_ERROR));

        $loader = new ModuleLoader($this->modulesPath, $this->providerChecker, coreVersion: '1.0.0');

        $this->expectException(InvalidModuleException::class);
        $this->expectExceptionMessage('requires core version "2.0.0"');

        $loader->discover();
    }

    public function test_it_throws_on_duplicate_module_names(): void
    {
        $this->writeManifest('First', $this->validManifest('Duplicate', priority: 1));
        $this->writeManifest('Second', $this->validManifest('Duplicate', priority: 2));

        $this->expectException(InvalidModuleException::class);
        $this->expectExceptionMessage('Duplicate module name');

        $this->loader()->discover();
    }

    public function test_it_allows_modules_in_the_same_priority_tier(): void
    {
        $this->writeManifest('Authentication', $this->validManifest('Authentication', priority: 1));
        $this->writeManifest('Authorization', $this->validManifest('Authorization', priority: 1));
        $this->writeManifest('AI', $this->validManifest('AI', priority: 500));
        $this->writeManifest('Automation', $this->validManifest('Automation', priority: 500));

        $manifests = $this->loader()->discover();

        self::assertCount(4, $manifests);
        self::assertSame(
            [1, 1, 500, 500],
            $manifests->map(static fn (ModuleManifest $m): int => $m->priority)->sort()->values()->all(),
        );
    }

    public function test_it_throws_when_a_dependency_is_not_installed(): void
    {
        $this->writeManifest('Dependent', $this->validManifest('Dependent', dependencies: ['Missing']));

        $this->expectException(InvalidModuleException::class);
        $this->expectExceptionMessage('depends on "Missing"');

        $this->loader()->discover();
    }

    public function test_it_resolves_satisfied_dependencies(): void
    {
        $this->writeManifest('Core', $this->validManifest('Core', priority: 1));
        $this->writeManifest('Auth', $this->validManifest('Auth', priority: 2, dependencies: ['Core']));

        self::assertCount(2, $this->loader()->discover());
    }

    private function loader(?CacheContract $cache = null, ?Dispatcher $events = null): ModuleLoader
    {
        return new ModuleLoader(
            modulesPath: $this->modulesPath,
            providerChecker: $this->providerChecker,
            cache: $cache,
            events: $events,
            coreVersion: '1.0.0',
        );
    }

    private function cache(): CacheContract
    {
        return new CacheRepository(new ArrayStore());
    }

    /**
     * @param list<string> $dependencies
     */
    private function validManifest(
        string $name,
        int $priority = 1,
        bool $enabled = true,
        ?string $uuid = null,
        array $dependencies = [],
    ): string {
        $manifest = [
            'name' => $name,
            'version' => '1.0.0',
            'minimumCoreVersion' => '1.0.0',
            'provider' => sprintf('Modules\\%s\\Providers\\%sServiceProvider', $name, $name),
            'enabled' => $enabled,
            'priority' => $priority,
            'dependencies' => $dependencies,
            'authors' => [['name' => 'AxiomOS Team']],
        ];

        if ($uuid !== null) {
            $manifest['uuid'] = $uuid;
        }

        return json_encode($manifest, JSON_THROW_ON_ERROR);
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
