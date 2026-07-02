<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\Services;

use Modules\Accounting\Domain\Models\FiscalYear;
use Modules\Accounting\Domain\Repositories\Contracts\FiscalYearRepositoryInterface;

final class FiscalYearService
{
    public function __construct(
        private readonly FiscalYearRepositoryInterface $years,
        private readonly AccountingPlatformHooks $hooks,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function list(string $companyId): array
    {
        return $this->years->byCompany($companyId)
            ->map(static fn (FiscalYear $year): array => $year->toArray())
            ->all();
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    public function create(array $attributes): array
    {
        $year = FiscalYear::query()->create($attributes);
        $this->hooks->onCreated($year);

        return $year->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function close(string $fiscalYearId): array
    {
        /** @var FiscalYear $year */
        $year = FiscalYear::query()->findOrFail($fiscalYearId);
        $before = $year->toAuditSnapshot();
        $year->fill(['is_closed' => true])->save();
        $this->hooks->onUpdated($year, $before);

        return $year->toArray();
    }
}
