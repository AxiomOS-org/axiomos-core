<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Configuration;

use App\Core\Configuration\ConfigurationBuilder;
use App\Core\Configuration\ConfigurationManager;
use App\Core\Configuration\ConfigurationSource;
use App\Core\Configuration\Events\ConfigurationLoaded;
use App\Core\Configuration\Events\ConfigurationLoading;
use App\Core\Configuration\Events\ConfigurationReloaded;
use App\Core\Configuration\Events\ConfigurationReloading;
use App\Core\Configuration\Events\ConfigurationValidationFailed;
use App\Core\Configuration\Exceptions\InvalidConfigurationException;
use Illuminate\Events\Dispatcher;
use PHPUnit\Framework\TestCase;

final class ConfigurationManagerTest extends TestCase
{
    private string $basePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basePath = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'axiomos_config_'
            . uniqid('', true);

        mkdir($this->basePath, 0777, true);
        mkdir($this->basePath . DIRECTORY_SEPARATOR . 'config', 0777, true);
        mkdir($this->basePath . DIRECTORY_SEPARATOR . 'modules', 0777, true);
        mkdir($this->basePath . DIRECTORY_SEPARATOR . 'plugins', 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->basePath);

        parent::tearDown();
    }

    public function test_it_loads_file_and_environment_configuration(): void
    {
        $this->writeFile('config/app.php', "<?php\nreturn ['name' => 'FileApp', 'env' => 'local'];\n");
        $this->writeFile('.env', "APP_NAME=EnvApp\nAPP_ENV=local\n");

        $configuration = $this->manager();
        $configuration->load();

        self::assertSame('EnvApp', $configuration->get('app.name'));
        self::assertSame('local', $configuration->get('app.env'));
        self::assertSame(ConfigurationSource::Environment, $configuration->sourceOf('app.name'));
    }

    public function test_it_loads_database_module_and_plugin_configuration(): void
    {
        $this->writeFile('config/app.php', "<?php\nreturn ['name' => 'AxiomOS', 'env' => 'local'];\n");
        $this->writeModuleConfig('Authentication', "<?php\nreturn ['enabled' => true];\n");
        $this->writePluginConfig('Billing', "<?php\nreturn ['currency' => 'USD'];\n");

        $configuration = ConfigurationBuilder::create($this->basePath)
            ->withDatabaseResolver(static fn (): array => ['database' => ['host' => 'db.internal']])
            ->build();

        $configuration->load();

        self::assertTrue($configuration->get('modules.Authentication.enabled'));
        self::assertSame('USD', $configuration->get('plugins.Billing.currency'));
        self::assertSame('db.internal', $configuration->get('database.host'));
    }

    public function test_runtime_configuration_overrides_everything(): void
    {
        $this->writeFile('config/app.php', "<?php\nreturn ['name' => 'AxiomOS', 'env' => 'local'];\n");

        $configuration = $this->manager();
        $configuration->load();
        $configuration->set('app.name', 'RuntimeOverride');

        self::assertSame('RuntimeOverride', $configuration->get('app.name'));
        self::assertSame(ConfigurationSource::Runtime, $configuration->sourceOf('app.name'));
    }

    public function test_it_validates_required_configuration(): void
    {
        $this->writeFile('config/other.php', "<?php\nreturn ['value' => 1];\n");

        $this->expectException(InvalidConfigurationException::class);

        $this->manager()->load();
    }

    public function test_it_reloads_configuration(): void
    {
        $this->writeFile('config/app.php', "<?php\nreturn ['name' => 'AxiomOS', 'env' => 'local'];\n");

        $configuration = $this->manager();
        $configuration->load();

        $this->writeFile('config/app.php', "<?php\nreturn ['name' => 'Reloaded', 'env' => 'local'];\n");
        $configuration->reload();

        self::assertSame('Reloaded', $configuration->get('app.name'));
    }

    public function test_it_caches_and_restores_configuration(): void
    {
        $this->writeFile('config/app.php', "<?php\nreturn ['name' => 'AxiomOS', 'env' => 'local'];\n");

        $cachePath = $this->basePath . DIRECTORY_SEPARATOR . 'cache.php';
        $configuration = $this->manager();
        $configuration->load();
        $configuration->cache($cachePath);

        $fresh = $this->manager();
        self::assertTrue($fresh->loadCache($cachePath));
        self::assertSame('AxiomOS', $fresh->get('app.name'));

        $fresh->clearCache($cachePath);
        self::assertFileDoesNotExist($cachePath);
    }

    public function test_it_fires_configuration_events(): void
    {
        $this->writeFile('config/app.php', "<?php\nreturn ['name' => 'AxiomOS', 'env' => 'local'];\n");

        $events = new Dispatcher();
        $captured = [];
        $events->listen('*', static function (string $event) use (&$captured): void {
            $captured[] = $event;
        });

        $configuration = ConfigurationBuilder::create($this->basePath)
            ->withEventDispatcher($events)
            ->build();

        $configuration->load();
        $configuration->reload();

        self::assertContains(ConfigurationLoading::class, $captured);
        self::assertContains(ConfigurationLoaded::class, $captured);
        self::assertContains(ConfigurationReloading::class, $captured);
        self::assertContains(ConfigurationReloaded::class, $captured);
    }

    public function test_it_fires_validation_failed_events(): void
    {
        $events = new Dispatcher();
        $failed = false;
        $events->listen(ConfigurationValidationFailed::class, static function () use (&$failed): void {
            $failed = true;
        });

        $configuration = ConfigurationBuilder::create($this->basePath)
            ->withEventDispatcher($events)
            ->build();

        try {
            $configuration->load();
        } catch (InvalidConfigurationException) {
            self::assertTrue($failed);
        }
    }

    private function manager(): ConfigurationManager
    {
        return ConfigurationBuilder::create($this->basePath)->build();
    }

    private function writeFile(string $relativePath, string $contents): void
    {
        $path = $this->basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($path, $contents);
    }

    private function writeModuleConfig(string $module, string $contents): void
    {
        $directory = $this->basePath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'Config';
        mkdir($directory, 0777, true);
        file_put_contents($directory . DIRECTORY_SEPARATOR . 'settings.php', $contents);
    }

    private function writePluginConfig(string $plugin, string $contents): void
    {
        $directory = $this->basePath . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR . 'Config';
        mkdir($directory, 0777, true);
        file_put_contents($directory . DIRECTORY_SEPARATOR . 'settings.php', $contents);
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
