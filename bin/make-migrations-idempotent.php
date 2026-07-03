<?php

declare(strict_types=1);

$modules = ['Sales', 'Purchase', 'Inventory', 'HR', 'CRM', 'Projects', 'Manufacturing', 'POS', 'FixedAssets', 'Budgeting', 'Reporting'];

foreach ($modules as $module) {
    $dir = dirname(__DIR__) . "/modules/{$module}/Database/Migrations";
    foreach (glob($dir . '/*.php') ?: [] as $file) {
        $content = (string) file_get_contents($file);
        $content = preg_replace(
            "/Schema::create\\('([^']+)'/",
            "if (! Schema::hasTable('$1')) { Schema::create('$1'",
            $content,
        ) ?? $content;
        $content = preg_replace(
            '/\$table->softDeletes\(\);\n        \}\);/',
            "\$table->softDeletes();\n        }); }\n",
            $content,
        ) ?? $content;
        file_put_contents($file, $content);
        echo "Idempotent: {$file}\n";
    }
}
