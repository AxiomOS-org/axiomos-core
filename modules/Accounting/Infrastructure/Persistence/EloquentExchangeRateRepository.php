<?php
declare(strict_types=1);
namespace Modules\Accounting\Infrastructure\Persistence;
use Illuminate\Support\Collection; use Modules\Accounting\Domain\Models\ExchangeRate; use Modules\Accounting\Domain\Repositories\Contracts\ExchangeRateRepositoryInterface; final class EloquentExchangeRateRepository implements ExchangeRateRepositoryInterface { public function byCompany(string $companyId): Collection { return ExchangeRate::query()->where('company_id',$companyId)->orderByDesc('rate_date')->get(); } public function latest(string $companyId,string $base,string $quote): ?ExchangeRate { return ExchangeRate::query()->where('company_id',$companyId)->where('base_currency',strtoupper($base))->where('quote_currency',strtoupper($quote))->orderByDesc('rate_date')->first(); } }

