<?php

declare(strict_types=1);

/** Repairs corrupted ERP migration files. */

$modules = ['Sales', 'Purchase', 'Inventory', 'HR', 'CRM', 'Projects', 'Manufacturing', 'POS', 'FixedAssets', 'Budgeting', 'Reporting'];

foreach ($modules as $module) {
    $dir = dirname(__DIR__) . "/modules/{$module}/Database/Migrations";
    foreach (glob($dir . '/*.php') ?: [] as $file) {
        $content = (string) file_get_contents($file);
        if (! preg_match_all("/Schema::create\('([^']+)'/", $content, $matches)) {
            continue;
        }

        $tables = $matches[1];
        $upBody = '';
        foreach ($tables as $table) {
            if (! preg_match("/Schema::create\\('{$table}'[\\s\\S]*?\\}\\);/", $content, $block)) {
                continue;
            }
            $upBody .= '        ' . trim($block[0]) . "\n";
        }

        $down = '';
        foreach (array_reverse($tables) as $table) {
            $down .= "        Schema::dropIfExists('{$table}');\n";
        }

        $fixed = <<<PHP
<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
{$upBody}    }
    public function down(): void {
{$down}    }
};

PHP;
        file_put_contents($file, $fixed);
        echo "Fixed migration: {$file}\n";
    }
}
