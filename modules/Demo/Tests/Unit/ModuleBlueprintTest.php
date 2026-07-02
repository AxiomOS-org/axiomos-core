<?php

declare(strict_types=1);

namespace Modules\Demo\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class ModuleBlueprintTest extends TestCase
{
    public function test_module_blueprint_is_bootstrappable(): void
    {
        self::assertTrue(class_exists(\Modules\Demo\Providers\DemoServiceProvider::class));
    }
}
