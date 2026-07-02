<?php

declare(strict_types=1);

namespace Tests\Stability;

use App\Infrastructure\Database\DatabaseBootstrap;
use Illuminate\Http\Request;
use Modules\Organization\Domain\Models\Organization;
use RuntimeException;
use Tests\Support\Stability\KernelTestHarness;

final class TransactionRollbackTest extends KernelTestHarness
{
    public function test_database_transaction_rolls_back_on_exception(): void
    {
        $this->kernel->handle(Request::create('/health', 'GET'));

        $connection = DatabaseBootstrap::capsule()->getConnection();
        $before = Organization::query()->count();

        $connection->beginTransaction();

        try {
            Organization::query()->create([
                'code' => 'rollback-probe-' . bin2hex(random_bytes(3)),
                'name' => 'Rollback Probe',
                'status' => 'active',
            ]);

            throw new RuntimeException('Forced rollback probe.');
        } catch (RuntimeException) {
            $connection->rollBack();
        }

        self::assertSame($before, Organization::query()->count());
    }
}
