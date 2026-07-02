<?php

declare(strict_types=1);

namespace Tests\Support\QA;

use App\Infrastructure\Database\DatabaseBootstrap;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Http\Request;
use Tests\Support\Stability\KernelTestHarness;

/**
 * Performance probes against PostgreSQL and HTTP kernel.
 */
class PerformanceProbe extends KernelTestHarness
{
    public function assertForeignKeyColumnsIndexed(): void
    {
        $connection = DatabaseBootstrap::capsule()->getConnection();
        $schema = (string) (getenv('TEST_SCHEMA') ?: 'axiomos_test_suite');

        $missing = [];

        $foreignKeys = $connection->select(<<<'SQL'
SELECT
    tc.table_name,
    kcu.column_name
FROM information_schema.table_constraints tc
JOIN information_schema.key_column_usage kcu
    ON tc.constraint_name = kcu.constraint_name
    AND tc.table_schema = kcu.table_schema
WHERE tc.constraint_type = 'FOREIGN KEY'
  AND tc.table_schema = ?
SQL, [$schema]);

        foreach ($foreignKeys as $foreignKey) {
            $table = (string) $foreignKey->table_name;
            $column = (string) $foreignKey->column_name;

            if (str_starts_with($table, 'universal_')) {
                continue;
            }

            $indexed = $connection->selectOne(<<<'SQL'
SELECT COUNT(*) AS aggregate
FROM pg_class t
JOIN pg_namespace n ON n.oid = t.relnamespace
JOIN pg_index ix ON t.oid = ix.indrelid
JOIN pg_attribute a ON a.attrelid = t.oid AND a.attnum = ANY(ix.indkey) AND a.attnum > 0
WHERE n.nspname = ?
  AND t.relname = ?
  AND a.attname = ?
SQL, [$schema, $table, $column]);

            if ((int) ($indexed->aggregate ?? 0) === 0) {
                $missing[] = sprintf('%s.%s', $table, $column);
            }
        }

        self::assertSame([], $missing, 'FK columns missing indexes: ' . implode(', ', $missing));
    }

    public function assertListQueryUsesIndex(): void
    {
        $this->warmKernel();
        $connection = DatabaseBootstrap::capsule()->getConnection();
        $schema = (string) (getenv('TEST_SCHEMA') ?: 'axiomos_test_suite');
        $connection->statement(sprintf('SET search_path TO "%s"', $schema));

        $plan = $connection->selectOne('EXPLAIN (FORMAT JSON) SELECT id FROM organizations ORDER BY created_at DESC LIMIT 15');
        $json = json_encode($plan, JSON_THROW_ON_ERROR);
        self::assertStringNotContainsString('Seq Scan on organizations', $json);
    }

    public function assertUsersListQueryCountIsBounded(): void
    {
        $this->warmKernel();
        $capsule = DatabaseBootstrap::capsule();
        $connection = $capsule->getConnection();
        $connection->enableQueryLog();

        $this->kernel->handle(Request::create('/api/users?page=1&per_page=15', 'GET'));

        $queryCount = count($connection->getQueryLog());
        self::assertLessThanOrEqual(12, $queryCount, 'Potential N+1: ' . $queryCount . ' queries for users list');
    }

    public function assertHealthRequestIsFast(): void
    {
        $started = hrtime(true);
        $response = $this->kernel->handle(Request::create('/health', 'GET'));
        $elapsedMs = (hrtime(true) - $started) / 1_000_000;

        self::assertSame(200, $response->getStatusCode());
        self::assertLessThan(15000.0, $elapsedMs, 'Health endpoint exceeded 15s');
    }

    public function assertMemoryDeltaIsBounded(): void
    {
        $before = memory_get_usage(true);
        $this->kernel->handle(Request::create('/api/organizations?page=1&per_page=15', 'GET'));
        $delta = memory_get_usage(true) - $before;

        self::assertLessThan(32 * 1024 * 1024, $delta, 'Memory delta exceeded 32MB for organizations list');
    }
}
