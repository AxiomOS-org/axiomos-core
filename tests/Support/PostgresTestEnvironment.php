<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Infrastructure\Database\DatabaseBootstrap;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Resets PostgreSQL schema state between integration tests.
 */
final class PostgresTestEnvironment
{
    public static function syncEnvironmentVariables(): void
    {
        foreach ([
            'APP_ENV',
            'DB_CONNECTION',
            'DB_HOST',
            'DB_PORT',
            'DB_DATABASE',
            'DB_SCHEMA',
            'DB_USERNAME',
            'DB_PASSWORD',
            'DB_SSLMODE',
            'TEST_SCHEMA',
        ] as $variable) {
            $value = self::readEnvironmentValue($variable);

            if ($value === null) {
                continue;
            }

            putenv($variable . '=' . $value);
            $_ENV[$variable] = $value;
            $_SERVER[$variable] = $value;
        }
    }

    public static function wipePublicSchema(string $basePath): void
    {
        DatabaseBootstrap::reset();
        self::syncEnvironmentVariables();

        /** @var array<string, mixed> $config */
        $config = require $basePath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

        $capsule = DatabaseBootstrap::boot($config);
        self::dropAndRecreateSchema($capsule, self::schemaName());
        DatabaseBootstrap::reset();
    }

    public static function dropAndRecreateSchema(Capsule $capsule, string $schema): void
    {
        $connection = $capsule->getConnection();

        if ($schema !== 'public') {
            $connection->statement(sprintf('DROP SCHEMA IF EXISTS "%s" CASCADE', $schema));
            $connection->statement(sprintf('CREATE SCHEMA "%s"', $schema));
            $connection->statement(sprintf('SET search_path TO "%s", public', $schema));

            return;
        }

        $connection->unprepared(<<<'SQL'
DO $$ DECLARE
    statement RECORD;
BEGIN
    FOR statement IN
        SELECT format('DROP TABLE IF EXISTS %I.%I CASCADE', schemaname, tablename) AS command
        FROM pg_tables
        WHERE schemaname = 'public'
    LOOP
        EXECUTE statement.command;
    END LOOP;

    FOR statement IN
        SELECT format('DROP SEQUENCE IF EXISTS %I.%I CASCADE', sequence_schema, sequence_name) AS command
        FROM information_schema.sequences
        WHERE sequence_schema = 'public'
    LOOP
        EXECUTE statement.command;
    END LOOP;
END $$;
SQL);
        $connection->statement('SET search_path TO public');
    }

    private static function schemaName(): string
    {
        $value = self::readEnvironmentValue('TEST_SCHEMA');

        if ($value === null || trim($value) === '') {
            return 'public';
        }

        return preg_replace('/[^a-zA-Z0-9_]/', '_', $value) ?: 'public';
    }

    private static function readEnvironmentValue(string $variable): ?string
    {
        $value = getenv($variable);

        if ($value !== false && $value !== '') {
            return $value;
        }

        if (isset($_ENV[$variable]) && is_string($_ENV[$variable]) && $_ENV[$variable] !== '') {
            return $_ENV[$variable];
        }

        if (isset($_SERVER[$variable]) && is_string($_SERVER[$variable]) && $_SERVER[$variable] !== '') {
            return $_SERVER[$variable];
        }

        return null;
    }
}
