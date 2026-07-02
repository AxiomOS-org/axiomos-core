<?php

declare(strict_types=1);

namespace Tests\Feature\ADT;

use App\ADT\Commands\MakeModuleCommand;
use App\ADT\Console\ConsoleIOInterface;
use App\ADT\Release\ReleaseManager;
use PHPUnit\Framework\TestCase;

final class AdtCertificationTest extends TestCase
{
    private string $basePath;

    protected function setUp(): void
    {
        $this->basePath = dirname(__DIR__, 3);
    }

    public function test_demo_module_blueprint_meets_certification_requirements(): void
    {
        $demoPath = $this->basePath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Demo';

        if (! is_dir($demoPath)) {
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
            $exitCode = $command->run(['axiomos:make-module', 'Demo', '--yes']);
            self::assertSame(0, $exitCode);
        }

        foreach ([
            'module.json',
            'README.md',
            'ARCHITECTURE.md',
            'CHANGELOG.md',
            'TESTING.md',
            'Providers/DemoServiceProvider.php',
            'Database/Migrations/.gitkeep',
            'Domain/Models/.gitkeep',
            'Tests/Unit/ModuleBlueprintTest.php',
        ] as $relativePath) {
            self::assertFileExists($demoPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath));
        }

        self::assertTrue(class_exists(\Modules\Demo\Providers\DemoServiceProvider::class));
    }

    public function test_release_manager_reports_platform_readiness(): void
    {
        $report = (new ReleaseManager($this->basePath, '1.0.0'))->readiness();

        self::assertNotEmpty($report->checks);
        self::assertTrue($report->ready, implode(', ', $report->failures));
    }
}
