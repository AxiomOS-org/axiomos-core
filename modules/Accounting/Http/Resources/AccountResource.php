<?php

declare(strict_types=1);

namespace Modules\Accounting\Http\Resources;

use Modules\Accounting\Domain\Models\Account;

final class AccountResource
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(Account $account): array
    {
        return [
            'id' => (string) $account->id,
            'organization_id' => $account->organization_id,
            'company_id' => (string) $account->company_id,
            'account_code' => (string) $account->account_code,
            'account_name' => (string) $account->account_name,
            'account_type' => (string) $account->account_type,
            'parent_account_id' => $account->parent_account_id,
            'metadata' => $account->metadata,
            'created_at' => $account->created_at?->toIso8601String(),
            'updated_at' => $account->updated_at?->toIso8601String(),
        ];
    }
}
