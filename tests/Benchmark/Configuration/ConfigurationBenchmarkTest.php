<?php

declare(strict_types=1);

namespace Tests\Benchmark\Configuration;

use App\Core\Configuration\ConfigurationBuilder;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('benchmark')]
final class ConfigurationBenchmarkTest extends TestCase
{
    private const ITERATIONS = 10_000;

    public function test_benchmark_configuration_get(): void
    {
        $basePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'axiomos_config_bench_' . uniqid('', true);
        mkdir($basePath . DIRECTORY_SEPARATOR . 'config', 0777, true);
        file_put_contents(
            $basePath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php',
            "<?php\nreturn ['name' => 'AxiomOS', 'env' => 'local'];\n",
        );

        $configuration = ConfigurationBuilder::create($basePath)->build();
        $configuration->load();

        $start = hrtime(true);

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $configuration->get('app.name');
        }

        $elapsedMs = (hrtime(true) - $start) / 1_000_000;

        $this->removeDirectory($basePath);

        self::assertLessThan(250.0, $elapsedMs, sprintf(
            'Configuration get for %d iterations took %.2f ms (budget: 250 ms).',
            self::ITERATIONS,
            $elapsedMs,
        ));
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
