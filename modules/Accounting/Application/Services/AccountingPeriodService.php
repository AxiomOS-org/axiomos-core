<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\Services;

use Modules\Accounting\Application\Support\AccountingTime;
use Modules\Accounting\Domain\Models\AccountingPeriod;
use Modules\Accounting\Domain\Repositories\Contracts\PeriodRepositoryInterface;

final class AccountingPeriodService
{
    public function __construct(
        private readonly PeriodRepositoryInterface $periods,
        private readonly AccountingPlatformHooks $hooks,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function list(string $companyId): array
    {
        return $this->periods->byCompany($companyId)
            ->map(static fn (AccountingPeriod $period): array => $period->toArray())
            ->all();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findOpenForDate(string $companyId, string $date): ?array
    {
        $period = $this->periods->findOpenForDate($companyId, $date);

        return $period?->toArray();
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    public function create(array $attributes): array
    {
        $period = AccountingPeriod::query()->create($attributes);
        $this->hooks->onCreated($period);

        return $period->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function setOpen(string $periodId, bool $open): array
    {
        /** @var AccountingPeriod $period */
        $period = AccountingPeriod::query()->findOrFail($periodId);
        $before = $period->toAuditSnapshot();
        $period->fill([
            'is_open' => $open,
            'closed_at' => $open ? null : AccountingTime::now(),
        ])->save();
        $this->hooks->onUpdated($period, $before);

        return $period->toArray();
    }
}
