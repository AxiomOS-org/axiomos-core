<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\Services;

use Modules\Accounting\Domain\Models\VoucherType;
use Modules\Accounting\Domain\Repositories\Contracts\VoucherTypeRepositoryInterface;

final class VoucherEngine
{
    public function __construct(private readonly VoucherTypeRepositoryInterface $voucherTypes)
    {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function list(string $companyId): array
    {
        return $this->voucherTypes->byCompany($companyId)
            ->map(static fn (VoucherType $type): array => $type->toArray())
            ->all();
    }

    public function nextNumber(string $companyId, string $code, string $postingDate): string
    {
        $type = $this->voucherTypes->byCompany($companyId)
            ->first(static fn (VoucherType $voucherType): bool => strcasecmp((string) $voucherType->code, $code) === 0);

        $pattern = $type?->series_pattern ?? strtoupper($code) . '/{FY}/{SEQ}';
        $year = date('Y', strtotime($postingDate));
        $sequence = random_int(1, 999999);

        return str_replace(
            ['{FY}', '{SEQ}', '{CODE}'],
            [$year, str_pad((string) $sequence, 6, '0', STR_PAD_LEFT), strtoupper($code)],
            $pattern,
        );
    }
}
