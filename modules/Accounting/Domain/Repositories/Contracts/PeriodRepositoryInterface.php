<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Repositories\Contracts;
use Illuminate\Support\Collection; use Modules\Accounting\Domain\Models\AccountingPeriod; interface PeriodRepositoryInterface { public function findOpenForDate(string $companyId, string $date): ?AccountingPeriod; public function byCompany(string $companyId): Collection; }

