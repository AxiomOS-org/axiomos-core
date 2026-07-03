<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\QueryException;
use Illuminate\Filesystem\Filesystem;

/**
 * Runs module migrations without Artisan.
 */
final class MigrationRunner
{
    public function __construct(
        private readonly MigrationRepositoryInterface $repository,
        private readonly Migrator $migrator,
    ) {
    }

    /**
     * @param list<string> $paths
     */
    public function run(array $paths): void
    {
        $this->ensureRepository();

        try {
            $this->migrator->run($paths);
        } catch (QueryException $exception) {
            if (! self::isMissingMigrationsTable($exception)) {
                throw $exception;
            }

            $this->repository->createRepository();
            $this->migrator->run($paths);
        }
    }

    private function ensureRepository(): void
    {
        if ($this->repository->repositoryExists()) {
            return;
        }

        try {
            $this->repository->createRepository();
        } catch (QueryException $exception) {
            if ($this->repository->repositoryExists()) {
                return;
            }

            if (! self::isRecoverableRepositoryError($exception)) {
                throw $exception;
            }

            $this->repository->createRepository();
        }
    }

    private static function isRecoverableRepositoryError(QueryException $exception): bool
    {
        $message = $exception->getMessage();

        return self::isMissingMigrationsTable($exception)
            || str_contains($message, 'no schema has been selected')
            || str_contains($message, 'pg_type_typname_nsp_index');
    }

    private static function isMissingMigrationsTable(QueryException $exception): bool
    {
        $message = $exception->getMessage();

        return str_contains($message, 'relation "migrations" does not exist')
            || str_contains($message, 'relation "axiomos_test_suite.migrations" does not exist');
    }

    /**
     * @param list<string> $paths
     */
    public function runIfNeeded(array $paths): void
    {
        $this->run($paths);
    }

    public static function create(Capsule $capsule, ?string $connection = null): self
    {
        $files = new Filesystem();
        $resolver = $capsule->getDatabaseManager();
        $connection ??= $resolver->getDefaultConnection();
        $repository = new DatabaseMigrationRepository($resolver, 'migrations');
        $migrator = new Migrator(
            $repository,
            $resolver,
            $files,
            $resolver->connection($connection)->getEventDispatcher(),
        );

        return new self($repository, $migrator);
    }
}
