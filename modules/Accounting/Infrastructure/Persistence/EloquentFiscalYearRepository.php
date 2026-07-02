<?php
declare(strict_types=1);
namespace Modules\Accounting\Infrastructure\Persistence;
use Illuminate\Support\Collection; use Modules\Accounting\Domain\Models\FiscalYear; use Modules\Accounting\Domain\Repositories\Contracts\FiscalYearRepositoryInterface; final class EloquentFiscalYearRepository implements FiscalYearRepositoryInterface { public function byCompany(string $companyId): Collection { return FiscalYear::query()->where('company_id',$companyId)->orderBy('start_date')->get(); } }

