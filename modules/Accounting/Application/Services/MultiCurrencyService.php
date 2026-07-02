<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\Services;

use Modules\Accounting\Domain\Models\ExchangeRate;
use Modules\Accounting\Domain\Repositories\Contracts\ExchangeRateRepositoryInterface;

final class MultiCurrencyService
{
    public function __construct(
        private readonly ExchangeRateRepositoryInterface $rates,
        private readonly AccountingPlatformHooks $hooks,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listRates(string $companyId): array
    {
        return $this->rates->byCompany($companyId)
            ->map(static fn (ExchangeRate $rate): array => $rate->toArray())
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function convert(string $companyId, string $amount, string $from, string $to, ?string $date = null): array
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return [
                'amount' => $amount,
                'converted_amount' => $amount,
                'rate' => '1.00000000',
                'base_currency' => $from,
                'quote_currency' => $to,
            ];
        }

        $rate = $this->rates->latest($companyId, $from, $to)
            ?? $this->rates->latest($companyId, $to, $from);

        if ($rate === null) {
            throw new \RuntimeException(sprintf('No exchange rate found for %s/%s.', $from, $to));
        }

        $appliedRate = (string) $rate->rate;
        $converted = strtoupper((string) $rate->base_currency) === $from
            ? MoneyMath::mul($amount, $appliedRate)
            : MoneyMath::div($amount, $appliedRate);

        return [
            'amount' => $amount,
            'converted_amount' => $converted,
            'rate' => $appliedRate,
            'base_currency' => (string) $rate->base_currency,
            'quote_currency' => (string) $rate->quote_currency,
            'rate_date' => (string) $rate->rate_date,
        ];
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    public function storeRate(array $attributes): array
    {
        $rate = ExchangeRate::query()->create($attributes);
        $this->hooks->onCreated($rate);

        return $rate->toArray();
    }
}
