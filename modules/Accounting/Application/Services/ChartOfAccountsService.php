<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\Services;

use Modules\Accounting\Domain\Models\Account;
use Modules\Accounting\Domain\Repositories\Contracts\AccountRepositoryInterface;

final class ChartOfAccountsService
{
    public function __construct(
        private readonly AccountRepositoryInterface $accounts,
        private readonly AccountingPlatformHooks $hooks,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function list(string $companyId): array
    {
        return $this->accounts->byCompany($companyId)
            ->map(static fn (Account $account): array => $account->toArray())
            ->all();
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    public function create(array $attributes): array
    {
        $attributes['status'] = $attributes['status'] ?? 'active';
        $account = $this->accounts->create($attributes);
        $this->hooks->onCreated($account);

        return $account->toArray();
    }
}
