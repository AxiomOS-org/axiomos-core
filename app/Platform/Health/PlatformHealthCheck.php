<?php

declare(strict_types=1);

namespace App\Platform\Health;

use App\Core\Http\Health\HealthCheckInterface;
use App\Core\Http\Health\HealthResult;
use App\Core\Http\Health\HealthStatus;
use App\Platform\Support\PlatformCapabilities;
use Illuminate\Support\Facades\Schema;

final class PlatformHealthCheck implements HealthCheckInterface
{
    public function name(): string
    {
        return 'platform';
    }

    public function run(): HealthResult
    {
        $requiredTables = [
            'universal_audit_logs',
            'universal_activities',
            'universal_comments',
            'universal_tags',
            'universal_ai_contexts',
        ];

        $missing = array_values(array_filter(
            $requiredTables,
            static fn (string $table): bool => ! Schema::hasTable($table),
        ));

        if ($missing !== []) {
            return new HealthResult(
                name: $this->name(),
                status: HealthStatus::Down,
                message: 'Platform tables are missing.',
                data: [
                    'capabilities' => PlatformCapabilities::all(),
                    'missing_tables' => $missing,
                ],
            );
        }

        return new HealthResult(
            name: $this->name(),
            status: HealthStatus::Ok,
            message: sprintf('%d platform capabilities active.', PlatformCapabilities::count()),
            data: [
                'capabilities' => PlatformCapabilities::all(),
                'capability_count' => PlatformCapabilities::count(),
                'platform_tables' => count($requiredTables),
            ],
        );
    }
}

