<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

/**
 * Ensures PostgreSQL is available before the application boots the database layer.
 */
final class PostgresConnectionValidator
{
    /**
     * @param array<string, mixed> $config
     */
    public static function assertConfigured(array $config): void
    {
        $default = (string) ($config['default'] ?? 'pgsql');

        if ($default !== 'pgsql') {
            throw new \RuntimeException(
                'AxiomOS requires PostgreSQL. Set DB_CONNECTION=pgsql (SQLite is not supported in development).',
            );
        }

        if (! extension_loaded('pdo_pgsql')) {
            throw new \RuntimeException(
                'The pdo_pgsql PHP extension is required. Enable php_pdo_pgsql in php.ini.',
            );
        }

        $connection = $config['connections']['pgsql'] ?? null;

        if (! is_array($connection) || ($connection['driver'] ?? null) !== 'pgsql') {
            throw new \RuntimeException('PostgreSQL connection configuration is missing from config/database.php.');
        }
    }
}
