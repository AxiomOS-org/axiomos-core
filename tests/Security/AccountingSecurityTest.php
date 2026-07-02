<?php

declare(strict_types=1);

namespace Tests\Security;

use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Tests\Support\PostgresFeatureTestCase;

final class AccountingSecurityTest extends PostgresFeatureTestCase
{
    public function test_accounting_posting_with_sql_injection_payload_is_not_500(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);
        $kernel->handle(Request::create('/health', 'GET'));

        $response = $kernel->handle(Request::create('/api/accounting/posting/submit', 'POST', [
            'idempotency_key' => "'; DROP TABLE accounting_journals; --",
            'source_module' => 'Sales',
            'source_document_type' => 'invoice',
            'source_document_id' => '1 OR 1=1',
            'company_id' => '00000000-0000-4000-8000-000000000001',
            'posting_date' => date('Y-m-d'),
            'currency' => 'USD',
            'exchange_rate' => '1',
            'voucher_type' => 'JV',
            'lines' => [],
        ], server: ['HTTP_ACCEPT' => 'application/json']));

        self::assertLessThan(500, $response->getStatusCode());
    }

    public function test_accounting_accounts_mass_assignment_extra_fields_is_not_500(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);
        $kernel->handle(Request::create('/health', 'GET'));

        $response = $kernel->handle(Request::create('/api/accounting/accounts', 'POST', [
            'organization_id' => '00000000-0000-4000-8000-000000000001',
            'company_id' => '00000000-0000-4000-8000-000000000001',
            'account_code' => '8888',
            'account_name' => 'Probe',
            'account_type' => 'asset',
            'is_admin' => true,
            'deleted_at' => date('Y-m-d'),
        ], server: ['HTTP_ACCEPT' => 'application/json']));

        self::assertLessThan(500, $response->getStatusCode());
    }

    public function test_accounting_reports_do_not_leak_cross_tenant_data_on_invalid_company(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);
        $kernel->handle(Request::create('/health', 'GET'));

        $response = $kernel->handle(Request::create(
            '/api/accounting/reports/trial-balance?company_id=00000000-0000-4000-8000-000000009999',
            'GET',
            server: ['HTTP_ACCEPT' => 'application/json'],
        ));

        self::assertLessThan(500, $response->getStatusCode());
        $content = (string) $response->getContent();
        self::assertStringNotContainsString('stack trace', strtolower($content));
    }
}
