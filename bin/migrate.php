<?php

declare(strict_types=1);

use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;

require __DIR__ . '/../vendor/autoload.php';

$basePath = dirname(__DIR__);

$readDatabaseEnv = static function (string $key, ?string $default = null): ?string {
    $value = getenv($key);

    if ($value !== false && $value !== '') {
        return $value;
    }

    if (isset($_ENV[$key]) && is_string($_ENV[$key]) && $_ENV[$key] !== '') {
        return $_ENV[$key];
    }

    if (isset($_SERVER[$key]) && is_string($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return $_SERVER[$key];
    }

    return $default;
};

foreach ([
    'DB_CONNECTION' => 'pgsql',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '5432',
    'DB_DATABASE' => 'axiomos',
    'DB_USERNAME' => 'postgres',
    'DB_PASSWORD' => 'postgres',
] as $key => $default) {
    if ($readDatabaseEnv($key) === null) {
        putenv($key . '=' . $default);
        $_ENV[$key] = $default;
        $_SERVER[$key] = $default;
    }
}

/** @var array<string, mixed> $config */
$config = require $basePath . '/config/database.php';

$capsule = DatabaseBootstrap::boot($config);

$migrationPaths = glob($basePath . '/modules/*/Database/Migrations', GLOB_ONLYDIR) ?: [];
sort($migrationPaths);

MigrationRunner::create($capsule)->run($migrationPaths);

fwrite(STDOUT, "Migrations completed on database: " . (getenv('DB_DATABASE') ?: 'axiomos') . PHP_EOL);
