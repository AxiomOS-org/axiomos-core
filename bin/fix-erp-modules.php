<?php

declare(strict_types=1);

/** Fixes scaffolded ERP modules: constructor promotion + camelCase API methods. */

$base = dirname(__DIR__) . '/modules';
$modules = array_filter(scandir($base) ?: [], static fn (string $d): bool => ! in_array($d, ['.', '..', 'Accounting', 'Organization', 'Identity', 'Users', 'Authentication', 'Authorization', 'Membership', 'Core', 'Settings'], true) && is_dir($base . '/' . $d));

foreach ($modules as $module) {
    $apiController = "{$base}/{$module}/Http/Controllers/Api/{$module}ApiController.php";
    $routesFile = "{$base}/{$module}/routes.php";

    if (! is_file($apiController)) {
        continue;
    }

    $content = (string) file_get_contents($apiController);
    $content = preg_replace_callback(
        '/public function __construct\(\s*([\s\S]*?)\s*\) \{\}/',
        static function (array $m): string {
            $params = trim($m[1]);
            if ($params === '') {
                return 'public function __construct() {}';
            }

            return 'public function __construct(' . $params . ") {}\n";
        },
        $content,
    ) ?? $content;

    $content = preg_replace_callback(
        '/public function ([a-z]+)-([a-z-]+)\(/',
        static function (array $m): string {
            $parts = explode('-', $m[1] . '-' . $m[2]);
            $camel = $parts[0];
            for ($i = 1; $i < count($parts); $i++) {
                $camel .= ucfirst($parts[$i]);
            }

            return 'public function ' . $camel . '(';
        },
        $content,
    ) ?? $content;

    if (! str_contains($content, 'private readonly')) {
        $content = preg_replace_callback(
            '/public function __construct\(\s*([\s\S]*?)\s*\) \{\}/',
            static function (array $m): string {
                $lines = array_filter(array_map('trim', explode(',', $m[1])));
                $promoted = [];
                foreach ($lines as $line) {
                    if ($line === '') {
                        continue;
                    }
                    $promoted[] = 'private readonly ' . rtrim($line, ',') . ',';
                }

                return "public function __construct(\n        " . implode("\n        ", $promoted) . "\n    ) {}";
            },
            $content,
        ) ?? $content;
    }

    file_put_contents($apiController, $content);

    if (is_file($routesFile)) {
        $routes = (string) file_get_contents($routesFile);
        $routes = preg_replace_callback(
            '/\$api->([a-z]+)-([a-z-]+)\(/',
            static function (array $m): string {
                $parts = explode('-', $m[1] . '-' . $m[2]);
                $camel = $parts[0];
                for ($i = 1; $i < count($parts); $i++) {
                    $camel .= ucfirst($parts[$i]);
                }

                return '$api->' . $camel . '(';
            },
            $routes,
        ) ?? $routes;
        file_put_contents($routesFile, $routes);
    }

    echo "Fixed {$module}\n";
}

// Add SoftDeletes trait to models
foreach ($modules as $module) {
    $modelsDir = "{$base}/{$module}/Domain/Models";
    if (! is_dir($modelsDir)) {
        continue;
    }
    foreach (glob($modelsDir . '/*.php') ?: [] as $modelFile) {
        $model = (string) file_get_contents($modelFile);
        if (! str_contains($model, 'SoftDeletes')) {
            $model = str_replace(
                "use Illuminate\Database\Eloquent\Model;\n",
                "use Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\SoftDeletes;\n",
                $model,
            );
            $model = str_replace("    use HasUuids;\n", "    use HasUuids;\n    use SoftDeletes;\n", $model);
            file_put_contents($modelFile, $model);
        }
    }
}

echo "All fixes applied.\n";
