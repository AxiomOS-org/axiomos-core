<?php

declare(strict_types=1);

namespace Tests\Stability;

use Tests\Support\Stability\KernelTestHarness;
use Tests\Support\Stability\ViewProbe;

final class ViewCompilationTest extends KernelTestHarness
{
    public function test_all_module_blade_views_compile(): void
    {
        $failures = ViewProbe::compileAll($this->basePath, ViewProbe::viewDirectories($this->basePath));

        self::assertSame([], $failures, "Blade compilation failures:\n" . implode("\n", $failures));
    }

    public function test_module_blade_files_exist(): void
    {
        self::assertGreaterThan(5, count(ViewProbe::moduleViewPaths($this->basePath)));
    }
}
