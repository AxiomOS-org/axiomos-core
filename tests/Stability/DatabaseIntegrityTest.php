<?php

declare(strict_types=1);

namespace Tests\Stability;

use App\Infrastructure\Database\DatabaseBootstrap;
use Tests\Support\Stability\KernelTestHarness;

final class DatabaseIntegrityTest extends KernelTestHarness
{
    public function test_foreign_keys_exist_for_core_tables(): void
    {
        $connection = DatabaseBootstrap::capsule()->getConnection();
        $schema = (string) (getenv('TEST_SCHEMA') ?: 'axiomos_test_suite');

        $count = (int) $connection->selectOne(
            'SELECT COUNT(*) AS aggregate FROM information_schema.table_constraints WHERE constraint_type = ? AND table_schema = ?',
            ['FOREIGN KEY', $schema],
        )->aggregate;

        self::assertGreaterThan(10, $count, 'Expected foreign keys in the test schema.');
    }

    public function test_users_reference_existing_identities(): void
    {
        $connection = DatabaseBootstrap::capsule()->getConnection();

        $orphans = (int) $connection->selectOne(<<<'SQL'
SELECT COUNT(*) AS aggregate
FROM users u
LEFT JOIN identities i ON i.id = u.identity_id
WHERE u.identity_id IS NOT NULL AND i.id IS NULL
SQL)->aggregate;

        self::assertSame(0, $orphans, 'Orphan users without identities detected.');
    }
}
