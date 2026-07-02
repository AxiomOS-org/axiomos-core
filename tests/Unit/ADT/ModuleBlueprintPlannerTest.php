<?php

declare(strict_types=1);

namespace Tests\Unit\ADT;

use App\ADT\MakeModule\ModuleBlueprintPlanner;
use App\ADT\MakeModule\ModuleNameValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ModuleBlueprintPlannerTest extends TestCase
{
    public function test_it_plans_mandatory_documentation_and_structure(): void
    {
        $artifacts = (new ModuleBlueprintPlanner())->plan('Accounting');
        $paths = array_map(static fn ($artifact): string => $artifact->relativePath, $artifacts);

        foreach ([
            'module.json',
            'README.md',
            'ARCHITECTURE.md',
            'CHANGELOG.md',
            'TESTING.md',
            'ROADMAP.md',
            'TODO.md',
            'Providers/AccountingServiceProvider.php',
            'Database/Migrations/.gitkeep',
            'Domain/Models/.gitkeep',
            'Application/Services/.gitkeep',
            'Infrastructure/Persistence/.gitkeep',
            'Presentation/Views/.gitkeep',
            'API/Controllers/.gitkeep',
            'Tests/Unit/ModuleBlueprintTest.php',
            'DemoData/.gitkeep',
        ] as $requiredPath) {
            self::assertContains($requiredPath, $paths, "Missing blueprint artifact: {$requiredPath}");
        }
    }

    public function test_module_name_validator_rejects_invalid_names(): void
    {
        $validator = new ModuleNameValidator();

        $this->expectException(InvalidArgumentException::class);
        $validator->validate('accounting');
    }
}
