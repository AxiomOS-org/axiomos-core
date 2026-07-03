<?php

declare(strict_types=1);

$base = dirname(__DIR__, 3);
$replacement = <<<'PHP'
    protected function companyId(\Illuminate\Http\Request $request): string {
        $fromQuery = trim((string) $request->query('company_id', $request->input('company_id', '')));
        if ($fromQuery !== '') {
            return $fromQuery;
        }
        return trim((string) $request->headers->get('X-Company-Id', ''));
    }
PHP;

$old = "protected function companyId(\\Illuminate\\Http\\Request \$request): string {\n        return (string) (\$request->query('company_id', \$request->input('company_id', '')));\n    }";

foreach (glob($base . '/modules/*/Http/Controllers/Api/ApiController.php') ?: [] as $file) {
    $content = file_get_contents($file);
    if ($content === false || ! str_contains($content, 'function companyId')) {
        continue;
    }
    if (str_contains($content, 'X-Company-Id')) {
        continue;
    }
    $updated = str_replace($old, $replacement, $content);
    if ($updated !== $content) {
        file_put_contents($file, $updated);
        echo "Updated {$file}\n";
    }
}
