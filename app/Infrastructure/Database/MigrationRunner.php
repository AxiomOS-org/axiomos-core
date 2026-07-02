<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Migrations\Migrator;
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
        if (! $this->repository->repositoryExists()) {
            $this->repository->createRepository();
        }

        $this->migrator->run($paths);
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
