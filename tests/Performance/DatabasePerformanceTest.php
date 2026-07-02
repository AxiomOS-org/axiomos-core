<?php

declare(strict_types=1);

namespace Tests\Performance;

use Tests\Support\QA\PerformanceProbe;

final class DatabasePerformanceTest extends PerformanceProbe
{
    public function test_foreign_key_columns_are_indexed(): void
    {
        $this->assertForeignKeyColumnsIndexed();
    }

    public function test_organizations_list_uses_indexed_plan(): void
    {
        $this->assertListQueryUsesIndex();
    }

    public function test_users_list_avoids_n_plus_one(): void
    {
        $this->assertUsersListQueryCountIsBounded();
    }
}
