<?php

declare(strict_types=1);

namespace Tests\Stability;

use App\Infrastructure\View\BladeBootstrap;
use Tests\Support\Stability\KernelTestHarness;
use Tests\Support\Stability\ViewProbe;

final class ViewCompilationTest extends KernelTestHarness
{
    public function test_all_module_blade_views_compile(): void
    {
        $failures = ViewProbe::compileAll($this->basePath, ViewProbe::viewDirectories($this->basePath));

        self::assertSame([], $failures, "Blade compilation failures:\n" . implode("\n", $failures));
    }

    public function test_all_module_blade_views_compile_with_strict_data(): void
    {
        BladeBootstrap::reset();

        $cachePath = $this->basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views';
        $factory = \App\Infrastructure\View\BladeBootstrap::boot($cachePath, ViewProbe::viewDirectories($this->basePath));
        $failures = [];

        foreach (ViewProbe::discoverViewNames($this->basePath) as $viewName) {
            try {
                $factory->make($viewName, ViewProbe::strictViewData())->render();
            } catch (\Throwable $exception) {
                $failures[] = $viewName . ': ' . $exception->getMessage();
            }
        }

        self::assertSame([], $failures, "Strict view validation failures:\n" . implode("\n", $failures));
    }
}
