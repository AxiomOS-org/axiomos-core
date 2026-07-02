<?php
declare(strict_types=1);
namespace Modules\Accounting\Infrastructure\Persistence;
use Illuminate\Support\Collection; use Modules\Accounting\Domain\Models\AccountingPeriod; use Modules\Accounting\Domain\Repositories\Contracts\PeriodRepositoryInterface; final class EloquentPeriodRepository implements PeriodRepositoryInterface { public function findOpenForDate(string $companyId,string $date): ?AccountingPeriod { return AccountingPeriod::query()->where('company_id',$companyId)->where('is_open',true)->whereDate('start_date','<=',$date)->whereDate('end_date','>=',$date)->first(); } public function byCompany(string $companyId): Collection { return AccountingPeriod::query()->where('company_id',$companyId)->orderBy('start_date')->get(); } }

