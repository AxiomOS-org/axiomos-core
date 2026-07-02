<?php

declare(strict_types=1);

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

return [
    'default' => $readDatabaseEnv('DB_CONNECTION', 'pgsql'),
    'connections' => [
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => $readDatabaseEnv('DB_HOST', '127.0.0.1'),
            'port' => $readDatabaseEnv('DB_PORT', '5432'),
            'database' => $readDatabaseEnv('DB_DATABASE', 'axiomos'),
            'username' => $readDatabaseEnv('DB_USERNAME', 'postgres'),
            'password' => $readDatabaseEnv('DB_PASSWORD', 'postgres'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => $readDatabaseEnv('DB_SCHEMA', $readDatabaseEnv('TEST_SCHEMA', 'public')),
            'sslmode' => $readDatabaseEnv('DB_SSLMODE', 'prefer'),
            'search_path' => $readDatabaseEnv('DB_SCHEMA', $readDatabaseEnv('TEST_SCHEMA', 'public')),
        ],
    ],
];
