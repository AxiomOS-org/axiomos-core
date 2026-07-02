<?php

declare(strict_types=1);

namespace Tests\Feature\ADT;

use App\ADT\Commands\MakeModuleCommand;
use App\ADT\Console\ConsoleIOInterface;
use App\ADT\MakeModule\ModuleConflictDetector;
use PHPUnit\Framework\TestCase;

final class MakeModuleCommandTest extends TestCase
{
    private string $basePath;

    private string $modulesPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basePath = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'axiomos_adt_'
            . uniqid('', true);

        $this->modulesPath = $this->basePath . DIRECTORY_SEPARATOR . 'modules';
        mkdir($this->modulesPath, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->basePath);

        parent::tearDown();
    }

    public function test_simulation_only_does_not_write_files(): void
    {
        $io = new class implements ConsoleIOInterface {
            public array $lines = [];

            public function writeln(string $message = ''): void
            {
                $this->lines[] = $message;
            }

            public function askYesNo(string $prompt): bool
            {
                return false;
            }
        };

        $command = new MakeModuleCommand($this->basePath, $io);
        $exitCode = $command->run(['axiomos:make-module', 'Accounting', '--simulation-only']);

        self::assertSame(0, $exitCode);
        self::assertFalse(is_dir($this->modulesPath . DIRECTORY_SEPARATOR . 'Accounting'));
        self::assertStringContainsString('Simulation Report', implode(PHP_EOL, $io->lines));
        self::assertStringContainsString('Module Name:', implode(PHP_EOL, $io->lines));
    }

    public function test_yes_flag_writes_blueprint_files(): void
    {
        $io = new class implements ConsoleIOInterface {
            public function writeln(string $message = ''): void
            {
            }

            public function askYesNo(string $prompt): bool
            {
                return false;
            }
        };

        $command = new MakeModuleCommand($this->basePath, $io);
        $exitCode = $command->run(['axiomos:make-module', 'Accounting', '--yes']);

        self::assertSame(0, $exitCode);

        $modulePath = $this->modulesPath . DIRECTORY_SEPARATOR . 'Accounting';

        foreach ([
            'module.json',
            'README.md',
            'ARCHITECTURE.md',
            'CHANGELOG.md',
            'TESTING.md',
            'Providers/AccountingServiceProvider.php',
        ] as $relativePath) {
            self::assertFileExists($modulePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath));
        }
    }

    public function test_conflict_detector_blocks_existing_module_path(): void
    {
        mkdir($this->modulesPath . DIRECTORY_SEPARATOR . 'Accounting', 0777, true);

        $conflicts = (new ModuleConflictDetector($this->modulesPath))->detect('Accounting');

        self::assertNotEmpty($conflicts);
    }

    private function removeDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $items = scandir($directory);

        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
    }
}
