<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Repositories\Contracts;
use Illuminate\Support\Collection; use Modules\Accounting\Domain\Models\ExchangeRate; interface ExchangeRateRepositoryInterface { public function byCompany(string $companyId): Collection; public function latest(string $companyId, string $base, string $quote): ?ExchangeRate; }

