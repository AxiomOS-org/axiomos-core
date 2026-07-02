<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\Services;

use Modules\Accounting\Domain\Models\Dimension;
use Modules\Accounting\Domain\Repositories\Contracts\DimensionRepositoryInterface;

final class CostCenterService
{
    public function __construct(
        private readonly DimensionRepositoryInterface $dimensions,
        private readonly AccountingPlatformHooks $hooks,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function costCenters(string $companyId): array
    {
        return $this->dimensions->byCompanyAndType($companyId, 'cost_center')
            ->map(static fn (Dimension $dimension): array => $dimension->toArray())
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function profitCenters(string $companyId): array
    {
        return $this->dimensions->byCompanyAndType($companyId, 'profit_center')
            ->map(static fn (Dimension $dimension): array => $dimension->toArray())
            ->all();
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    public function create(array $attributes): array
    {
        $dimension = Dimension::query()->create($attributes);
        $this->hooks->onCreated($dimension);

        return $dimension->toArray();
    }
}
