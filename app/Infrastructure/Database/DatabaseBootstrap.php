<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\Facade;

/**
 * Boots Eloquent outside a full Laravel application.
 */
final class DatabaseBootstrap
{
    private static bool $booted = false;

    private static ?Capsule $capsule = null;

    /**
     * @param array<string, mixed> $config
     */
    public static function boot(array $config): Capsule
    {
        if (self::$booted && self::$capsule !== null) {
            return self::$capsule;
        }

        PostgresConnectionValidator::assertConfigured($config);

        $default = (string) ($config['default'] ?? 'pgsql');
        $connections = $config['connections'] ?? [];

        $capsule = new Capsule();

        foreach ($connections as $name => $connection) {
            $capsule->addConnection($connection, (string) $name);
        }

        $capsule->getDatabaseManager()->setDefaultConnection($default);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        self::applySearchPath($capsule, $connections[$default] ?? []);

        $container = $capsule->getContainer();
        $container->instance('db', $capsule->getDatabaseManager());
        $container->singleton('db.schema', static fn () => $capsule->connection()->getSchemaBuilder());
        Facade::setFacadeApplication($container);

        self::$booted = true;
        self::$capsule = $capsule;

        return $capsule;
    }

    public static function capsule(): Capsule
    {
        if (self::$capsule === null) {
            throw new \RuntimeException('Database has not been booted.');
        }

        return self::$capsule;
    }

    public static function reset(): void
    {
        if (self::$capsule !== null) {
            self::$capsule->getDatabaseManager()->disconnect();
        }

        Facade::clearResolvedInstances();

        self::$booted = false;
        self::$capsule = null;
    }

    /**
     * @param array<string, mixed> $connection
     */
    private static function applySearchPath(Capsule $capsule, array $connection): void
    {
        $schema = $connection['search_path'] ?? $connection['schema'] ?? null;

        if (! is_string($schema) || $schema === '') {
            return;
        }

        $capsule->getConnection()->statement(sprintf('SET search_path TO "%s"', str_replace('"', '', $schema)));
    }
}
