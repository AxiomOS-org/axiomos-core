<?php

declare(strict_types=1);

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\Accounting\Application\DTOs\PostingRequest;
use Modules\Accounting\Application\Support\AccountingTime;
use Modules\Accounting\Domain\Services\Contracts\PostingEngineInterface;

final class AccountingDemoSeeder
{
    public function __construct(private readonly PostingEngineInterface $posting)
    {
    }

    public function run(): void
    {
        if (! Schema::hasTable('companies') || ! Schema::hasTable('accounting_accounts')) {
            return;
        }

        /** @var Collection<int, object{id: string, organization_id: string|null}> $companies */
        $companies = DB::table('companies')->orderBy('code')->get(['id', 'organization_id']);
        if ($companies->isEmpty()) {
            return;
        }

        $timestamp = AccountingTime::now();
        $firstCompanyId = null;

        foreach ($companies as $company) {
            $companyId = (string) $company->id;
            $firstCompanyId ??= $companyId;
            $this->seedCompanyFoundation($companyId, $company->organization_id, $timestamp);
        }

        if ($firstCompanyId !== null) {
            $this->seedDemoPosting($firstCompanyId);
        }
    }

    private function seedCompanyFoundation(string $companyId, mixed $organizationId, \Illuminate\Support\Carbon $timestamp): void
    {
        if (DB::table('accounting_accounts')->where('company_id', $companyId)->count() === 0) {
            DB::table('accounting_accounts')->insert([
                [
                    'id' => (string) Str::uuid(),
                    'organization_id' => $organizationId,
                    'company_id' => $companyId,
                    'account_code' => '1000',
                    'account_name' => 'Cash',
                    'account_type' => 'asset',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'id' => (string) Str::uuid(),
                    'organization_id' => $organizationId,
                    'company_id' => $companyId,
                    'account_code' => '4000',
                    'account_name' => 'Sales Revenue',
                    'account_type' => 'income',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
            ]);
        }

        if (DB::table('accounting_fiscal_years')->where('company_id', $companyId)->count() === 0) {
            DB::table('accounting_fiscal_years')->insert([
                'id' => (string) Str::uuid(),
                'organization_id' => $organizationId,
                'company_id' => $companyId,
                'name' => 'FY-' . date('Y'),
                'start_date' => date('Y-01-01'),
                'end_date' => date('Y-12-31'),
                'is_closed' => false,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        $fiscalYearId = (string) DB::table('accounting_fiscal_years')->where('company_id', $companyId)->value('id');

        if (DB::table('accounting_periods')->where('company_id', $companyId)->count() === 0) {
            DB::table('accounting_periods')->insert([
                'id' => (string) Str::uuid(),
                'organization_id' => $organizationId,
                'company_id' => $companyId,
                'fiscal_year_id' => $fiscalYearId,
                'name' => date('Y-m'),
                'start_date' => date('Y-m-01'),
                'end_date' => date('Y-m-t'),
                'is_open' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        if (DB::table('accounting_voucher_types')->where('company_id', $companyId)->count() === 0) {
            DB::table('accounting_voucher_types')->insert([
                'id' => (string) Str::uuid(),
                'organization_id' => $organizationId,
                'company_id' => $companyId,
                'code' => 'JV',
                'name' => 'Journal Voucher',
                'series_pattern' => 'JV/{FY}/{SEQ}',
                'auto_approve' => true,
                'is_active' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }

    private function seedDemoPosting(string $companyId): void
    {
        $organizationId = DB::table('companies')->where('id', $companyId)->value('organization_id');
        $cash = (string) DB::table('accounting_accounts')->where('company_id', $companyId)->where('account_code', '1000')->value('id');
        $revenue = (string) DB::table('accounting_accounts')->where('company_id', $companyId)->where('account_code', '4000')->value('id');

        if ($cash === '' || $revenue === '') {
            return;
        }

        $this->posting->submit(new PostingRequest(
            'seed:accounting:demo:' . $companyId,
            'Accounting',
            'seed_demo',
            'seed-' . $companyId,
            $companyId,
            is_string($organizationId) ? $organizationId : null,
            null,
            null,
            date('Y-m-d'),
            'USD',
            '1',
            'JV',
            [
                ['account_id' => $cash, 'debit' => '1000.000000', 'credit' => '0.000000'],
                ['account_id' => $revenue, 'debit' => '0.000000', 'credit' => '1000.000000'],
            ],
            'Accounting demo seed posting',
        ));
    }
}
